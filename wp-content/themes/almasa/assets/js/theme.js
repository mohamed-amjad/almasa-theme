(function () {
  'use strict';

  var doc = document.documentElement;
  var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  if (reduce) {
    doc.classList.add('almasa-reduced-motion');
  }
  doc.classList.add('almasa-js');

  function pad(n) {
    return (n < 10 ? '0' : '') + n;
  }

  /* Elements inside `scope` (including scope itself) that were not initialised yet. */
  function fresh(scope, selector, key) {
    var list = Array.prototype.slice.call(scope.querySelectorAll(selector));
    if (scope.matches && scope.matches(selector)) {
      list.unshift(scope);
    }
    return list.filter(function (el) {
      var flag = 'almasa' + key;
      if (el.dataset[flag]) {
        return false;
      }
      el.dataset[flag] = '1';
      return true;
    });
  }

  /* Header */
  function initHeader(header) {
    var onScroll = function () {
      header.classList.toggle('is-scrolled', window.scrollY > 40);
    };
    onScroll();
    window.addEventListener('scroll', onScroll, { passive: true });

    var toggle = header.querySelector('[data-almasa-nav-toggle]');
    var nav = header.querySelector('[data-almasa-nav]');
    if (!toggle || !nav) {
      return;
    }
    var setNav = function (open) {
      nav.classList.toggle('is-open', open);
      document.body.classList.toggle('almasa-nav-open', open);
      toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    };
    toggle.addEventListener('click', function () {
      setNav(!nav.classList.contains('is-open'));
    });
    nav.addEventListener('click', function (e) {
      if (e.target.closest('a')) {
        setNav(false);
      }
    });
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && nav.classList.contains('is-open')) {
        setNav(false);
        toggle.focus();
      }
    });
  }

  /* Hero slideshow */
  function initHero(hero) {
    var slides = hero.querySelectorAll('[data-almasa-slide]');
    var interval = parseInt(hero.getAttribute('data-interval'), 10) || 6000;
    var index = 0;
    var timer = null;

    var show = function (i) {
      index = (i + slides.length) % slides.length;
      slides.forEach(function (slide, n) {
        slide.classList.toggle('is-active', n === index);
      });
    };

    var start = function () {
      clearInterval(timer);
      if (reduce || slides.length < 2 || !hero.isConnected) {
        return;
      }
      timer = setInterval(function () {
        show(index + 1);
      }, interval);
    };

    document.addEventListener('visibilitychange', function () {
      if (document.hidden) {
        clearInterval(timer);
      } else {
        start();
      }
    });

    start();
  }

  /* Reveal on scroll + counters */
  var countUp = function (el) {
    var target = parseInt(el.getAttribute('data-almasa-count'), 10) || 0;
    if (reduce || target < 2) {
      el.textContent = pad(target);
      return;
    }
    var startTime = null;
    var duration = 1200;
    var step = function (t) {
      if (!startTime) {
        startTime = t;
      }
      var p = Math.min((t - startTime) / duration, 1);
      el.textContent = pad(Math.round(target * (1 - Math.pow(1 - p, 3))));
      if (p < 1) {
        requestAnimationFrame(step);
      }
    };
    requestAnimationFrame(step);
  };

  var io = null;
  if ('IntersectionObserver' in window && !reduce) {
    io = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (!entry.isIntersecting) {
          return;
        }
        entry.target.classList.add('is-visible');
        entry.target.querySelectorAll('[data-almasa-count]').forEach(countUp);
        io.unobserve(entry.target);
      });
    }, { rootMargin: '0px 0px -8% 0px', threshold: 0.12 });
  }

  function initReveal(el) {
    if (io) {
      io.observe(el);
      return;
    }
    el.classList.add('is-visible');
    el.querySelectorAll('[data-almasa-count]').forEach(countUp);
  }

  /* Sliders: native scroll-snap track, arrows loop, progress, autoplay */
  function initSlider(slider) {
    var track = slider.querySelector('[data-slider-track]');
    var controls = slider.querySelector('[data-slider-controls]');
    var bar = slider.querySelector('[data-slider-bar]');
    var prev = slider.querySelector('[data-slider-prev]');
    var next = slider.querySelector('[data-slider-next]');
    if (!track) {
      return;
    }
    var rtl = getComputedStyle(track).direction === 'rtl';
    var delay = parseInt(slider.getAttribute('data-autoplay'), 10) || 0;
    var timer = null;
    var paused = false;
    var inView = false;

    var maxScroll = function () {
      return Math.max(track.scrollWidth - track.clientWidth, 0);
    };
    var offset = function () {
      return Math.abs(track.scrollLeft);
    };
    var stepSize = function () {
      var first = track.firstElementChild;
      var gap = parseFloat(getComputedStyle(track).columnGap) || 0;
      return first ? first.getBoundingClientRect().width + gap : track.clientWidth;
    };
    var scrollToOffset = function (x) {
      track.scrollTo({ left: rtl ? -x : x, behavior: reduce ? 'auto' : 'smooth' });
    };

    var update = function () {
      var max = maxScroll();
      if (controls) {
        controls.hidden = max < 2;
      }
      if (bar) {
        var shown = track.clientWidth / track.scrollWidth;
        var p = max ? offset() / max : 1;
        bar.style.width = ((shown + (1 - shown) * p) * 100).toFixed(2) + '%';
      }
    };

    var go = function (dir) {
      var max = maxScroll();
      var x = offset();
      if (dir > 0) {
        scrollToOffset(x >= max - 4 ? 0 : Math.min(x + stepSize(), max));
      } else {
        scrollToOffset(x <= 4 ? max : Math.max(x - stepSize(), 0));
      }
    };

    var stop = function () {
      clearInterval(timer);
      timer = null;
    };
    var play = function () {
      stop();
      if (!delay || reduce || paused || !inView || document.hidden || !slider.isConnected || maxScroll() < 2) {
        return;
      }
      timer = setInterval(function () {
        go(1);
      }, delay);
    };

    if (prev) {
      prev.addEventListener('click', function () {
        go(-1);
        play();
      });
    }
    if (next) {
      next.addEventListener('click', function () {
        go(1);
        play();
      });
    }

    var raf = null;
    track.addEventListener('scroll', function () {
      if (!raf) {
        raf = requestAnimationFrame(function () {
          raf = null;
          update();
        });
      }
    }, { passive: true });

    ['mouseenter', 'focusin', 'touchstart'].forEach(function (ev) {
      slider.addEventListener(ev, function () {
        paused = true;
        stop();
      }, { passive: true });
    });
    ['mouseleave', 'focusout'].forEach(function (ev) {
      slider.addEventListener(ev, function () {
        paused = false;
        play();
      });
    });
    slider.addEventListener('touchend', function () {
      setTimeout(function () {
        paused = false;
        play();
      }, delay || 0);
    }, { passive: true });

    document.addEventListener('visibilitychange', play);
    window.addEventListener('resize', function () {
      update();
      play();
    });

    if ('IntersectionObserver' in window) {
      new IntersectionObserver(function (entries) {
        inView = entries[0].isIntersecting;
        play();
      }, { threshold: 0.3 }).observe(slider);
    } else {
      inView = true;
    }

    update();
    play();
  }

  /* Gallery: filter + load more */
  function initGallery(gallery) {
    var items = Array.prototype.slice.call(gallery.querySelectorAll('.almasa-photos__item'));
    var filters = gallery.querySelectorAll('[data-almasa-filter]');
    var more = gallery.querySelector('[data-almasa-more]');
    var limit = parseInt(gallery.getAttribute('data-limit'), 10) || 12;
    var step = limit;
    var active = 'all';
    var visible = limit;

    var render = function (animateFrom) {
      var matched = items.filter(function (item) {
        return active === 'all' || item.getAttribute('data-group') === active;
      });
      items.forEach(function (item) {
        item.hidden = true;
        item.classList.remove('is-entering');
        item.classList.toggle('is-filtered-out', matched.indexOf(item) === -1);
      });
      matched.forEach(function (item, n) {
        if (n < visible) {
          item.hidden = false;
          if (typeof animateFrom === 'number' && n >= animateFrom) {
            item.style.animationDelay = ((n - animateFrom) * 60) + 'ms';
            item.classList.add('is-entering');
          }
        }
      });
      if (more) {
        more.hidden = matched.length <= visible;
      }
    };

    filters.forEach(function (btn) {
      btn.addEventListener('click', function () {
        active = btn.getAttribute('data-almasa-filter');
        visible = limit;
        filters.forEach(function (b) {
          var on = b === btn;
          b.classList.toggle('is-active', on);
          b.setAttribute('aria-pressed', on ? 'true' : 'false');
        });
        render(0);
      });
    });

    if (more) {
      more.addEventListener('click', function () {
        var from = visible;
        visible += step;
        render(from);
      });
    }

    render();
  }

  /* Lightbox (one shared dialog, delegated clicks so re-rendered galleries work) */
  var lightbox = null;
  function getLightbox() {
    if (lightbox) {
      return lightbox;
    }
    var box = document.createElement('div');
    box.className = 'almasa-lightbox';
    box.setAttribute('role', 'dialog');
    box.setAttribute('aria-modal', 'true');
    box.setAttribute('aria-label', 'معرض الصور');
    box.innerHTML =
      '<div class="almasa-lightbox__top">' +
        '<span class="almasa-lightbox__count"></span>' +
        '<button type="button" class="almasa-lightbox__btn" data-lb-close aria-label="إغلاق">' +
          '<svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true"><path fill="none" stroke="currentColor" stroke-width="2" d="M6 6l12 12M18 6L6 18"/></svg>' +
        '</button>' +
      '</div>' +
      '<div class="almasa-lightbox__stage">' +
        '<button type="button" class="almasa-lightbox__btn almasa-lightbox__prev" data-lb-prev aria-label="السابق">' +
          '<svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true"><path fill="none" stroke="currentColor" stroke-width="2" d="M9 5l7 7-7 7"/></svg>' +
        '</button>' +
        '<img class="almasa-lightbox__img" alt="">' +
        '<button type="button" class="almasa-lightbox__btn almasa-lightbox__next" data-lb-next aria-label="التالي">' +
          '<svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true"><path fill="none" stroke="currentColor" stroke-width="2" d="M15 5l-7 7 7 7"/></svg>' +
        '</button>' +
      '</div>' +
      '<div class="almasa-lightbox__bottom"><p class="almasa-lightbox__caption"></p></div>';
    document.body.appendChild(box);

    var img = box.querySelector('.almasa-lightbox__img');
    var count = box.querySelector('.almasa-lightbox__count');
    var caption = box.querySelector('.almasa-lightbox__caption');
    var set = [];
    var pos = 0;
    var opener = null;
    var touchX = null;

    var load = function (i) {
      pos = (i + set.length) % set.length;
      var a = set[pos];
      img.classList.add('is-loading');
      var pre = new Image();
      pre.onload = pre.onerror = function () {
        img.src = a.getAttribute('href');
        img.alt = a.getAttribute('data-caption') || '';
        img.classList.remove('is-loading');
      };
      pre.src = a.getAttribute('href');
      count.textContent = pad(pos + 1) + ' / ' + pad(set.length);
      caption.textContent = a.getAttribute('data-caption') || '';
    };

    var close = function () {
      box.classList.remove('is-open');
      document.body.classList.remove('almasa-nav-open');
      if (opener) {
        opener.focus();
      }
    };

    box.querySelector('[data-lb-close]').addEventListener('click', close);
    box.querySelector('[data-lb-prev]').addEventListener('click', function () { load(pos - 1); });
    box.querySelector('[data-lb-next]').addEventListener('click', function () { load(pos + 1); });
    box.addEventListener('click', function (e) {
      if (e.target === box || e.target.classList.contains('almasa-lightbox__stage')) {
        close();
      }
    });

    document.addEventListener('keydown', function (e) {
      if (!box.classList.contains('is-open')) {
        return;
      }
      if (e.key === 'Escape') {
        close();
      } else if (e.key === 'ArrowLeft') {
        load(pos + 1);
      } else if (e.key === 'ArrowRight') {
        load(pos - 1);
      }
    });

    box.addEventListener('touchstart', function (e) {
      touchX = e.changedTouches[0].clientX;
    }, { passive: true });
    box.addEventListener('touchend', function (e) {
      if (touchX === null) {
        return;
      }
      var dx = e.changedTouches[0].clientX - touchX;
      if (Math.abs(dx) > 50) {
        load(dx > 0 ? pos + 1 : pos - 1);
      }
      touchX = null;
    }, { passive: true });

    lightbox = {
      open: function (a) {
        var scope = a.closest('[data-almasa-gallery]') || document;
        set = Array.prototype.slice.call(scope.querySelectorAll('[data-almasa-lightbox]')).filter(function (link) {
          var li = link.closest('.almasa-photos__item');
          return !li || !li.classList.contains('is-filtered-out');
        });
        opener = a;
        load(Math.max(set.indexOf(a), 0));
        box.classList.add('is-open');
        document.body.classList.add('almasa-nav-open');
        box.querySelector('[data-lb-close]').focus();
      }
    };
    return lightbox;
  }

  document.addEventListener('click', function (e) {
    var a = e.target.closest ? e.target.closest('[data-almasa-lightbox]') : null;
    if (!a) {
      return;
    }
    e.preventDefault();
    getLightbox().open(a);
  });

  /* Contact form: client validation (mirrors inc/contact-form.php) + AJAX submit */
  function initForm(form) {
    var cfg = window.almasaForm;
    if (!cfg || !window.fetch || !window.FormData) {
      return;
    }
    var msg = cfg.messages || {};
    var phoneRe = new RegExp(cfg.phonePattern);
    var emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;
    var status = (form.closest('.almasa-form-card') || document).querySelector('[data-form-status]');
    var submit = form.querySelector('[data-form-submit]');
    var label = form.querySelector('[data-submit-label]');
    var labelText = label ? label.textContent : '';

    var asciiDigits = function (value) {
      return value.replace(/[\u0660-\u0669]/g, function (d) {
        return String(d.charCodeAt(0) - 0x0660);
      }).replace(/[\u06F0-\u06F9]/g, function (d) {
        return String(d.charCodeAt(0) - 0x06F0);
      });
    };

    var rules = {
      name: function (v) {
        v = v.trim();
        if (v.length < 3) { return msg.name_short; }
        if (v.length > 80) { return msg.name_long; }
        return '';
      },
      phone: function (v) {
        v = asciiDigits(v).replace(/[\s\-().]/g, '');
        if (!v) { return msg.phone_empty; }
        return phoneRe.test(v) ? '' : msg.phone_invalid;
      },
      email: function (v) {
        v = v.trim();
        return !v || emailRe.test(v) ? '' : msg.email_invalid;
      },
      message: function (v) {
        v = v.trim();
        if (v.length < 10) { return msg.message_short; }
        if (v.length > 2000) { return msg.message_long; }
        return '';
      }
    };

    var setError = function (name, text) {
      var field = form.elements[name];
      var box = form.querySelector('[data-error-for="' + name + '"]');
      if (!field) {
        return;
      }
      var wrap = field.closest('.almasa-field');
      if (box) {
        box.textContent = text || '';
      }
      field.setAttribute('aria-invalid', text ? 'true' : 'false');
      if (wrap) {
        wrap.classList.toggle('is-invalid', !!text);
        wrap.classList.toggle('is-valid', !text && field.value.trim() !== '');
      }
    };

    var check = function (name) {
      var field = form.elements[name];
      var error = rules[name] && field ? rules[name](field.value) : '';
      setError(name, error);
      return !error;
    };

    var showStatus = function (text, ok) {
      if (!status) {
        return;
      }
      status.hidden = false;
      status.textContent = text;
      status.classList.toggle('is-success', !!ok);
      status.classList.toggle('is-error', !ok);
    };

    Object.keys(rules).forEach(function (name) {
      var field = form.elements[name];
      if (!field) {
        return;
      }
      field.addEventListener('blur', function () {
        if (field.value.trim() !== '' || field.dataset.touched) {
          field.dataset.touched = '1';
          check(name);
        }
      });
      field.addEventListener('input', function () {
        if (field.dataset.touched) {
          check(name);
        }
      });
    });

    var busy = function (on) {
      form.classList.toggle('is-sending', on);
      if (submit) {
        submit.disabled = on;
      }
      if (label) {
        label.textContent = on ? (msg.sending || labelText) : labelText;
      }
    };

    form.addEventListener('submit', function (e) {
      e.preventDefault();
      var firstBad = null;
      Object.keys(rules).forEach(function (name) {
        var field = form.elements[name];
        if (field) {
          field.dataset.touched = '1';
        }
        if (!check(name) && !firstBad) {
          firstBad = field;
        }
      });
      if (firstBad) {
        showStatus(msg.check_fields, false);
        firstBad.focus();
        return;
      }

      busy(true);
      fetch(cfg.ajaxUrl, { method: 'POST', body: new FormData(form), credentials: 'same-origin' })
        .then(function (res) {
          return res.json().catch(function () {
            return { success: false, data: { message: msg.failed } };
          });
        })
        .then(function (json) {
          var data = (json && json.data) || {};
          if (json && json.success) {
            form.reset();
            Object.keys(rules).forEach(function (name) {
              var field = form.elements[name];
              if (field) {
                delete field.dataset.touched;
              }
              setError(name, '');
            });
            showStatus(data.message || msg.sent, true);
            if (status) {
              status.scrollIntoView({ block: 'center', behavior: reduce ? 'auto' : 'smooth' });
            }
            return;
          }
          var errors = data.errors || {};
          Object.keys(errors).forEach(function (name) {
            setError(name, errors[name]);
          });
          showStatus(data.message || msg.failed, false);
        })
        .catch(function () {
          showStatus(msg.failed, false);
        })
        .then(function () {
          busy(false);
        });
    });
  }

  function init(scope) {
    scope = scope || document;
    fresh(scope, '[data-almasa-header]', 'Header').forEach(initHeader);
    fresh(scope, '[data-almasa-hero]', 'Hero').forEach(initHero);
    fresh(scope, '[data-reveal]', 'Reveal').forEach(initReveal);
    fresh(scope, '[data-almasa-slider]', 'Slider').forEach(initSlider);
    fresh(scope, '[data-almasa-gallery]', 'Gallery').forEach(initGallery);
    fresh(scope, '[data-almasa-form]', 'Form').forEach(initForm);
  }

  init(document);

  /* Elementor: re-initialise widgets rendered (or re-rendered in the editor) after load. */
  var hooked = false;
  function hookElementor() {
    var fe = window.elementorFrontend;
    if (hooked || !fe || !fe.hooks) {
      return;
    }
    hooked = true;
    fe.hooks.addAction('frontend/element_ready/widget', function ($scope) {
      init($scope && $scope[0] ? $scope[0] : document);
    });
  }
  hookElementor();
  window.addEventListener('elementor/frontend/init', hookElementor);
})();
