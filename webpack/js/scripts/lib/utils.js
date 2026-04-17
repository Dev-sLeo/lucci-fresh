"use strict";

import * as dom from "./dom.js";

const disableLink = (event) => {
    event.preventDefault();
};

const copyUrlToClipboard = (event) => {
    event.preventDefault();
    console.log("click in copy");
    dom.copyTextToClipboard(location.href);
};

export default function () {
    let linkDisabled = document.querySelectorAll(".js-disable-link");
    let linkCopyUrlToClipBoard = document.querySelectorAll(".js-copy-link");
    if (linkDisabled.length > 0) {
        if (linkDisabled[0].nodeName !== "A") {
            linkDisabled = document.querySelectorAll(".js-disable-link>a");
        }

        dom.forEachEl(linkDisabled, (el) => {
            el.addEventListener("click", disableLink);
        });
    }

    if (linkCopyUrlToClipBoard.length > 0) {
        dom.forEachEl(linkCopyUrlToClipBoard, (el) => {
            el.addEventListener("click", copyUrlToClipboard);
        });
    }
}
