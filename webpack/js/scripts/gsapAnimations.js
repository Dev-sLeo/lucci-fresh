import { gsap } from "gsap";

const DEFAULT_DURATION = 0.2;
const DEFAULT_DELAY = 0;
const DEFAULT_DISTANCE = 24;
const DEFAULT_EASE = "power2.out";
const DEFAULT_ROOT_MARGIN = "0px 0px -10% 0px";
const DEFAULT_THRESHOLD = 0.2;

const getNumber = (value, fallback) => {
    const parsed = Number.parseFloat(value);
    return Number.isFinite(parsed) ? parsed : fallback;
};

const getBoolean = (value, fallback) => {
    if (value === undefined) {
        return fallback;
    }

    return value !== "false";
};

const getEffectVars = (element) => {
    const effect = element.dataset.animate || "fade";
    const distance = getNumber(
        element.dataset.animateDistance,
        DEFAULT_DISTANCE,
    );

    switch (effect) {
        case "fade-up":
            return { opacity: 0, y: distance };
        case "fade-down":
            return { opacity: 0, y: -distance };
        case "fade-left":
            return { opacity: 0, x: -distance };
        case "fade-right":
            return { opacity: 0, x: distance };
        case "zoom-in":
            return { opacity: 0, scale: 0.96 };
        case "zoom-out":
            return { opacity: 0, scale: 1.04 };
        case "fade":
        default:
            return { opacity: 0 };
    }
};

const getTiming = (element) => ({
    duration: getNumber(element.dataset.animateDuration, DEFAULT_DURATION),
    delay: getNumber(element.dataset.animateDelay, DEFAULT_DELAY),
    ease: element.dataset.animateEase || DEFAULT_EASE,
});

const getToVars = (element) => {
    const timing = getTiming(element);

    return {
        opacity: 1,
        x: 0,
        y: 0,
        scale: 1,
        ...timing,
    };
};

const getObserverOptions = (element) => ({
    rootMargin: element.dataset.animateRootMargin || DEFAULT_ROOT_MARGIN,
    threshold: getNumber(element.dataset.animateThreshold, DEFAULT_THRESHOLD),
});

const getObserverKey = (options) =>
    `${options.rootMargin}|${options.threshold}`;

const supportsReducedMotion = () =>
    window.matchMedia("(prefers-reduced-motion: reduce)").matches;

const setupAnimations = () => {
    if (window.innerWidth <= 1024) {
        return;
    }

    const elements = Array.from(document.querySelectorAll("[data-animate]"));

    if (!elements.length) {
        return;
    }

    if (supportsReducedMotion() || !("IntersectionObserver" in window)) {
        elements.forEach((element) => {
            gsap.set(element, { ...getToVars(element), willChange: "auto" });
        });

        return;
    }

    const observers = new Map();

    const getObserver = (element) => {
        const options = getObserverOptions(element);
        const key = getObserverKey(options);

        if (observers.has(key)) {
            return observers.get(key);
        }

        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                const elementTarget = entry.target;
                const animateOnce = getBoolean(
                    elementTarget.dataset.animateOnce,
                    true,
                );

                if (entry.isIntersecting) {
                    gsap.to(elementTarget, {
                        ...getToVars(elementTarget),
                        overwrite: "auto",
                        onComplete: () => {
                            elementTarget.style.willChange = "auto";
                        },
                    });

                    if (animateOnce) {
                        observer.unobserve(elementTarget);
                    }
                } else if (!animateOnce) {
                    gsap.set(elementTarget, getEffectVars(elementTarget));
                }
            });
        }, options);

        observers.set(key, observer);
        return observer;
    };

    elements.forEach((element) => {
        gsap.set(element, {
            ...getEffectVars(element),
            willChange: "transform, opacity",
        });

        getObserver(element).observe(element);
    });
};

export default setupAnimations;
