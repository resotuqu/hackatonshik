import Cropper from "cropperjs";
import "cropperjs/dist/cropper.css";
import EasyMDE from "easymde";
import "easymde/dist/easymde.min.css";
import "iconify-icon";
import { addCollection } from "iconify-icon";
import heroicons from "@iconify-json/heroicons/icons.json";

addCollection(heroicons);

window.Cropper = Cropper;
window.EasyMDE = EasyMDE;

/**
 * Alpine x-data factory for profile / admin avatar cropping (Cropper.js + Livewire upload).
 *
 * @param {{ property: string, multiple: boolean, outputSize?: number, quality?: number }} config
 */
window.createAvatarCropperModal = function createAvatarCropperModal(config) {
    const outputSize = config.outputSize ?? 512;
    const quality = config.quality ?? 0.9;

    return {
        property: config.property,
        multiple: Boolean(config.multiple),
        outputSize,
        quality,
        state: "idle",
        cropper: null,
        blobUrl: null,
        dialogOpen: false,
        /** @type {File[]} */
        batch: [],
        batchCursor: 0,
        /** @type {File[]} */
        croppedOutputs: [],
        modalCaption: "",

        isSvg(file) {
            return (
                file.type === "image/svg+xml" || /\.svg$/i.test(file.name || "")
            );
        },

        pickFiles() {
            this.$refs.fileInput.click();
        },

        onFilesSelected(event) {
            const files = Array.from(event.target.files || []);
            event.target.value = "";
            if (!files.length) {
                return;
            }
            if (!window.Cropper) {
                console.warn("Cropper.js is not loaded.");
                return;
            }
            if (this.multiple) {
                this.startBatch(files);
                return;
            }
            const f = files[0];
            if (!f.type.startsWith("image/")) {
                return;
            }
            if (this.isSvg(f)) {
                this.uploadSingleRaw(f);
                return;
            }
            this.modalCaption = "";
            this.startCropForFile(f);
        },

        uploadSingleRaw(file) {
            this.state = "uploading";
            this.$wire.upload(
                this.property,
                file,
                () => {
                    this.state = "idle";
                },
                () => {
                    this.state = "error";
                },
                () => {},
            );
        },

        startBatch(files) {
            const batch = files.filter((f) => f.type.startsWith("image/"));
            if (!batch.length) {
                return;
            }
            this.batch = batch;
            this.batchCursor = 0;
            this.croppedOutputs = [];
            this.advanceBatch();
        },

        advanceBatch() {
            while (this.batchCursor < this.batch.length) {
                const f = this.batch[this.batchCursor];
                if (this.isSvg(f)) {
                    this.croppedOutputs.push(f);
                    this.batchCursor++;
                    continue;
                }
                this.modalCaption = `Файл ${this.batchCursor + 1} из ${this.batch.length}`;
                this.startCropForFile(f);
                return;
            }
            if (this.croppedOutputs.length) {
                this.finishMultiUpload();
            }
        },

        destroyCropper() {
            if (this.cropper) {
                this.cropper.destroy();
                this.cropper = null;
            }
            if (this.blobUrl) {
                URL.revokeObjectURL(this.blobUrl);
                this.blobUrl = null;
            }
            const img = this.$refs.cropImg;
            if (img) {
                img.onload = null;
                img.src = "";
            }
        },

        startCropForFile(file) {
            this.state = "loading";
            this.destroyCropper();
            this.blobUrl = URL.createObjectURL(file);
            const img = this.$refs.cropImg;
            img.onload = () => {
                const dialog = this.$refs.dialog;
                if (dialog && !dialog.open) {
                    dialog.showModal();
                }
                this.dialogOpen = true;
                this.$nextTick(() => {
                    this.cropper = new window.Cropper(img, {
                        aspectRatio: 1,
                        viewMode: 1,
                        dragMode: "move",
                        preview: this.$refs.previewBox,
                    });
                    this.state = "cropping";
                });
            };
            img.src = this.blobUrl;
        },

        applyCrop() {
            if (!this.cropper || this.state !== "cropping") {
                return;
            }
            const canvas = this.cropper.getCroppedCanvas({
                width: this.outputSize,
                height: this.outputSize,
                imageSmoothingEnabled: true,
                imageSmoothingQuality: "high",
            });
            if (!canvas) {
                this.state = "error";
                return;
            }
            canvas.toBlob(
                (blob) => {
                    if (!blob) {
                        this.state = "error";
                        return;
                    }
                    const name = `avatar-${Date.now()}-${Math.random().toString(36).slice(2, 10)}.jpg`;
                    const out = new File([blob], name, {
                        type: "image/jpeg",
                        lastModified: Date.now(),
                    });
                    if (!this.multiple) {
                        this.state = "uploading";
                        this.$wire.upload(
                            this.property,
                            out,
                            () => {
                                this.closeDialogAfterUpload();
                            },
                            () => {
                                this.state = "error";
                            },
                            () => {},
                        );
                        return;
                    }
                    this.croppedOutputs.push(out);
                    this.batchCursor++;
                    this.destroyCropper();
                    this.advanceBatch();
                },
                "image/jpeg",
                this.quality,
            );
        },

        finishMultiUpload() {
            if (!this.croppedOutputs.length) {
                return;
            }
            this.state = "uploading";
            this.$wire.uploadMultiple(
                this.property,
                this.croppedOutputs,
                () => {
                    this.closeDialogAfterUpload();
                },
                () => {
                    this.state = "error";
                },
                () => {},
                () => {},
                false,
            );
        },

        closeDialogAfterUpload() {
            this.destroyCropper();
            const dialog = this.$refs.dialog;
            if (dialog?.open) {
                dialog.close();
            }
            this.dialogOpen = false;
            this.state = "idle";
            this.batch = [];
            this.batchCursor = 0;
            this.croppedOutputs = [];
        },

        cancelCrop() {
            this.destroyCropper();
            const dialog = this.$refs.dialog;
            if (dialog?.open) {
                dialog.close();
            }
            this.dialogOpen = false;
            this.state = "idle";
            this.batch = [];
            this.batchCursor = 0;
            this.croppedOutputs = [];
        },

        onDialogClosed() {
            if (this.state === "uploading") {
                return;
            }
            this.destroyCropper();
            this.dialogOpen = false;
            if (this.state !== "idle") {
                this.state = "idle";
            }
            this.batch = [];
            this.batchCursor = 0;
            this.croppedOutputs = [];
        },
    };
};

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allow your team to quickly build robust real-time web applications.
 */

import { setupTabGroup } from './tabs';

window.setupTabGroup = setupTabGroup;
