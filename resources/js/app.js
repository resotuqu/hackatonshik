import mask from "@alpinejs/mask";
import Cropper from "cropperjs";
import "cropperjs/dist/cropper.css";
import EasyMDE from "easymde";
import "easymde/dist/easymde.min.css";
import "@fortawesome/fontawesome-free/css/all.min.css";
import "iconify-icon";
import { addCollection } from "iconify-icon";
import heroicons from "@iconify-json/heroicons/icons.json";
import "./avatar-cropper-modal.js";
import "./echo";
import { bootstrapTabSystem, initTabGroups, setupTabGroup } from "./tabs";

addCollection(heroicons);

window.Cropper = Cropper;
window.EasyMDE = EasyMDE;
window.setupTabGroup = setupTabGroup;

bootstrapTabSystem();

function bootstrapPageScripts() {
    initTabGroups(document);
}

document.addEventListener("DOMContentLoaded", bootstrapPageScripts);
document.addEventListener("livewire:navigated", () => {
    requestAnimationFrame(() => bootstrapPageScripts());
});

document.addEventListener("alpine:init", () => {
    window.Alpine.plugin(mask);
});

document.addEventListener("livewire:init", () => {
    window.Livewire.hook("morph.updated", ({ el }) => {
        if (el instanceof Element && (el.matches("[data-tab-init]") || el.querySelector("[data-tab-init]"))) {
            initTabGroups(el);
        }
    });
});

// Smooth scroll to top when wizard steps change
document.addEventListener("livewire:navigated", () => {
    const wizardForm = document.querySelector('[aria-label*="wizard"]');
    if (wizardForm) {
        requestAnimationFrame(() => {
            wizardForm.scrollIntoView({ behavior: "smooth", block: "start" });
        });
    }
});
