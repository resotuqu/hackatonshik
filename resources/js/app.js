import EasyMDE from "easymde";
import "easymde/dist/easymde.min.css";
import "iconify-icon";
import { addCollection } from "iconify-icon";
import heroicons from "@iconify-json/heroicons/icons.json";

addCollection(heroicons);

window.EasyMDE = EasyMDE;
