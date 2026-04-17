'use strict';

const scrollTo = (element, to, duration) => {
    if (duration <= 0) return;

    const difference = to - element.scrollTop;
    const perTick = difference / duration * 10;

    setTimeout(function() {
        element.scrollTop = element.scrollTop + perTick;
        if (element.scrollTop === to) return;
        scrollTo(element, to, duration - 10);
    }, 10);
};

export function scrollIt(destination, duration = 200, easing = 'linear', callback) {

  const easings = {
    linear(t) {
      return t;
    },
    easeInQuad(t) {
      return t * t;
    },
    easeOutQuad(t) {
      return t * (2 - t);
    },
    easeInOutQuad(t) {
      return t < 0.5 ? 2 * t * t : -1 + (4 - 2 * t) * t;
    },
    easeInCubic(t) {
      return t * t * t;
    },
    easeOutCubic(t) {
      return (--t) * t * t + 1;
    },
    easeInOutCubic(t) {
      return t < 0.5 ? 4 * t * t * t : (t - 1) * (2 * t - 2) * (2 * t - 2) + 1;
    },
    easeInQuart(t) {
      return t * t * t * t;
    },
    easeOutQuart(t) {
      return 1 - (--t) * t * t * t;
    },
    easeInOutQuart(t) {
      return t < 0.5 ? 8 * t * t * t * t : 1 - 8 * (--t) * t * t * t;
    },
    easeInQuint(t) {
      return t * t * t * t * t;
    },
    easeOutQuint(t) {
      return 1 + (--t) * t * t * t * t;
    },
    easeInOutQuint(t) {
      return t < 0.5 ? 16 * t * t * t * t * t : 1 + 16 * (--t) * t * t * t * t;
    }
  };

  const start = window.pageYOffset;
  const startTime = 'now' in window.performance ? performance.now() : new Date().getTime();

  const documentHeight = Math.max(document.body.scrollHeight, document.body.offsetHeight, document.documentElement.clientHeight, document.documentElement.scrollHeight, document.documentElement.offsetHeight);
  const windowHeight = window.innerHeight || document.documentElement.clientHeight || document.getElementsByTagName('body')[0].clientHeight;
  const destinationOffset = typeof destination === 'number' ? destination : destination.offsetTop - 120;
  const destinationOffsetToScroll = Math.round(documentHeight - destinationOffset < windowHeight ? documentHeight - windowHeight : destinationOffset);

  if ('requestAnimationFrame' in window === false) {
    window.scroll(0, destinationOffsetToScroll);
    if (callback) {
      callback();
    }
    return;
  }

  function scroll() {
    const now = 'now' in window.performance ? performance.now() : new Date().getTime();
    const time = Math.min(1, ((now - startTime) / duration));
    const timeFunction = easings[easing](time);
    window.scroll(0, Math.ceil((timeFunction * (destinationOffsetToScroll - start)) + start));

    if (window.pageYOffset === destinationOffsetToScroll) {
      if (callback) {
        callback();
      }
      return;
    }

    requestAnimationFrame(scroll);
  }

  scroll();
}

export function scrollToDirection(element, to, duration, direction) {
    var animating = false;
    if (animating || !element || to === undefined || !duration) { //stop when already triggered or missing args
        return false;
    }
    var _requestAnimationFrame = function(win, t) { return win["webkitR" + t] || win["r" + t] || win["mozR" + t] || win["msR" + t] || function(fn) { setTimeout(fn, 60); }; } (window, "requestAnimationFrame"),
        easeInOutCubic = function (t) { return t<0.5 ? 4*t*t*t : (t-1)*(2*t-2)*(2*t-2)+1; }, //Or get your own: http://gizma.com/easing/
        end = +new Date() + duration,
        from = (element === 'window') ? window.pageXOffset : element.scrollLeft;
        animating = true;

        if (direction === 'vertical') {
            from = (element === 'window') ? window.pageYOffset : element.scrollTop;
        }

    var step = function() {
        var current = +new Date(),
            remaining = end - current;

        if (remaining < 0) {
            animating = false;
        } else {
            var ease = easeInOutCubic(1 - remaining / duration);

            if (!direction || direction === "horizontal") {
                (element === 'window') ? window.scrollTo(from + (ease * (to-from)), window.pageYOffset) : element.scrollLeft = from + (ease * (to-from));
            } else if (direction === "vertical") {
                (element === 'window') ? window.scrollTo(window.pageXOffset, from + (ease * (to-from))) : element.scrollTop = from + (ease * (to-from));
            }
        }

        _requestAnimationFrame(step);
    };
    step();
};


export function isScrolledIntoView(el) {
    console.log(el);
    var rect = el.getBoundingClientRect();
    var elemTop = rect.top;
    var elemBottom = rect.bottom;

    // Only completely visible elements return true:
    // var isVisible = (elemTop >= 0) && (elemBottom <= window.innerHeight);
    // Partially visible elements return true:
    var isVisible = elemTop < window.innerHeight && elemBottom >= 0;
    console.log(isVisible);
    return isVisible;
}

export function getPosition(el) {
  let xPos = 0;
  let yPos = 0;

  while (el) {
    if (el.tagName == "BODY") {
      // deal with browser quirks with body/window/document and page scroll
      let xScroll = el.scrollLeft || document.documentElement.scrollLeft;
      let yScroll = el.scrollTop || document.documentElement.scrollTop;

      xPos += (el.offsetLeft - xScroll + el.clientLeft);
      yPos += (el.offsetTop - yScroll + el.clientTop);
    } else {
      // for all other non-BODY elements
      xPos += (el.offsetLeft - el.scrollLeft + el.clientLeft);
      yPos += (el.offsetTop - el.scrollTop + el.clientTop);
    }

    el = el.offsetParent;
  }
  return {
    x: xPos,
    y: yPos
  };
}

export function scrollDocumentTo(to, duration) {
    console.log(to, duration);
    scrollTo(document.body, to, duration);
}

export function forEachEl(els, callback) {
    const elCount = els.length;
    let i, boundCallback;

    for (i = 0; i< elCount; i++) {
        callback(els[i], i);
    }
}

export function hasClass(el, klass) {
    if (el.classList) {
        return el.classList.contains(klass);
    }

    return new RegExp('(^| )' + klass + '( |$)', 'gi').test(el.className);
}

export function addClass(el, klass) {
    if (el.classList) {
        return el.classList.add(klass);
    }

    return el.className += ' ' + klass;
}

export function removeClass(el, klass) {
    if (el.classList) {
        return el.classList.remove(klass);
    }

    return el.className = el.className.replace(new RegExp('(^|\\b)' + klass.split(' ').join('|') + '(\\b|$)', 'gi'), ' ');
}

export function findTarget(el, klass) {
    if (hasClass(el, klass)) { return el; }
    return parents(el, klass);
}

export function parents(el, klass) {
    if (!el) { return null; }

    let found = false;
    let targetEl = null;
    let parentEl = el.parentNode;

    let index = 0;

    while (targetEl === null) {
        index++;
        if (index > 3000) { break; }
        if (!parentEl) { break; }

        if (parentEl.tagName === 'BODY' && !hasClass(parentEl, klass)) { break; }

        if (!hasClass(parentEl, klass)) {
            parentEl = parentEl.parentNode;
            continue;
        }

        targetEl = parentEl;
    }

    return targetEl;
}

export function cumulativeOffset(element) {
    var top = 0, left = 0;
    do {
        top += element.offsetTop  || 0;
        left += element.offsetLeft || 0;
        element = element.offsetParent;
    } while(element);

    return {
        top: top,
        left: left
    };
};


export function getRandomInteger(min, max) {
  return Math.floor(Math.random() * (max - min + 1)) + min;
}

export function copyTextToClipboard(text) {
  var textArea = document.createElement("textarea");

  //
  // *** This styling is an extra step which is likely not required. ***
  //
  // Why is it here? To ensure:
  // 1. the element is able to have focus and selection.
  // 2. if element was to flash render it has minimal visual impact.
  // 3. less flakyness with selection and copying which **might** occur if
  //    the textarea element is not visible.
  //
  // The likelihood is the element won't even render, not even a flash,
  // so some of these are just precautions. However in IE the element
  // is visible whilst the popup box asking the user for permission for
  // the web page to copy to the clipboard.
  //

  // Place in top-left corner of screen regardless of scroll position.
  textArea.style.position = 'fixed';
  textArea.style.top = 0;
  textArea.style.left = 0;

  // Ensure it has a small width and height. Setting to 1px / 1em
  // doesn't work as this gives a negative w/h on some browsers.
  textArea.style.width = '2em';
  textArea.style.height = '2em';

  // We don't need padding, reducing the size if it does flash render.
  textArea.style.padding = 0;

  // Clean up any borders.
  textArea.style.border = 'none';
  textArea.style.outline = 'none';
  textArea.style.boxShadow = 'none';

  // Avoid flash of white box if rendered for any reason.
  textArea.style.background = 'transparent';


  textArea.value = text;

  document.body.appendChild(textArea);

  textArea.select();

  try {
    var successful = document.execCommand('copy');
    var msg = successful ? 'successful' : 'unsuccessful';
    console.log('Copying text command was ' + msg);
    if (msg === 'successful') {
        alert('URL copiada para área de transferência');
    }
  } catch (err) {
    console.log('Oops, unable to copy');
  }

  document.body.removeChild(textArea);
}
