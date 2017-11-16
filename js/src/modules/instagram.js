/**
 * need instafeed.js plugin to be included in vendor.js
 * TODO: import instafeed.js from packages instead of packing to vendor.js file
 *
 * markup example:
 *
 * <div class="instawidget-container">
 *   <div class="wrapper">
 *     <div class="instawidget-wrapper">
 *       <div class="instawidget js-instawidget">
 *         <div class="instawidget-page js-instawidget-page is-active" id="instafeed"></div>
 *       </div>
 *       <div class="instawidget-prev js-instawidget-prev is-disabled"></div>
 *       <div class="instawidget-next js-instawidget-next"></div>
 *     </div>
 *   </div>
 * </div>
 *
 * initialization example:
 *
 * const instagram = new InstagramWidget({
 *   limit: {
 *     default: 12,
 *     mobile: 4,
 *   },
 * });
 *
 */

import { $WINDOW } from './globals';
import { IS_SMALL_MOBILE_WIDTH, status } from './helpers';

export default class InstagramWidget {
  constructor({
    widgetClass = 'js-instawidget',
    pageClass = 'js-instawidget-page',
    nextPageTriggerClass = 'js-instawidget-next',
    prevPageTriggerClass = 'js-instawidget-prev',
    limit = { default: 12, mobile: 12 },
  } = {}) {
    this.SETTINGS_URL = '/api/settings/instagram';
    this.STARTUP_TIMEOUT = 1000;

    Object.assign(this, {
      widgetClass,
      pageClass,
      nextPageTriggerClass,
      prevPageTriggerClass,
      limit,
    });

    this.$widget = $(`.${this.widgetClass}`);

    this.pageWidth = this.$widget.find(`.${this.pageClass}`).width();

    this.options = {
      get: 'user',
      resolution: 'low_resolution',
      template: '<a href="{{link}}" target="_blank"><img src="{{image}}" alt="" /></a>',
      after: () => this.nextPageCallback(),
      limit: IS_SMALL_MOBILE_WIDTH() ? this.limit.mobile : this.limit.default,
    };

    this.initFeed();
  }

  getParams() {
    return fetch(this.SETTINGS_URL)
      .then(status)
      .then((params) => params.json())
      .catch((error) => {
        console.warn('An error occurred when trying to get instagram parameters. Please check params object at the backend.');
        return Promise.reject()
      });
  }

  initFeed() {
    if (!this.$widget.length) return;

    this.getParams().then((params) => {
      this.options = { ...this.options, ...params };
      this.feed = new Instafeed(this.options);
      this.feed.run();
      setTimeout(() => this.feed.next(), this.STARTUP_TIMEOUT);
      this.bindClickNext();
      this.bindClickPrev();
      this.bindWindowResize();
    }).catch(() => null);
  }

  nextPageCallback() {
    const pics_limit = this.options.limit - 1;
    const $first_page = this.$widget.find(`.${this.pageClass}:first`);
    const page_classes = $first_page[0].className.replace(' is-active', '')
    const $new_page = $(`<div class="${page_classes}"></div>`);
    const $new_pics = $first_page.find(`a:gt(${pics_limit})`)
      .detach();
    $new_page.append($new_pics);
    if ($new_page.find('a').length) {
      this.$widget.append($new_page);
    }
  }

  bindClickNext() {
    const $next_page_trigger = $(`.${this.nextPageTriggerClass}`);
    const $prev_page_trigger = $(`.${this.prevPageTriggerClass}`);

    $next_page_trigger.click((e) => {
      e.preventDefault();

      this.feed.next();

      let $active_page = this.$widget.find(`.${this.pageClass}.is-active`);
      let current_position = parseInt(this.$widget.css('left'), 10);

      if ($active_page.next().length) {
        $active_page = $active_page.removeClass('is-active').next().addClass('is-active');
        this.$widget.css('left', current_position - this.pageWidth );
      }
      if ($active_page.prev().length) {
        $prev_page_trigger.removeClass('is-disabled');
      }
      if (this.feed.hasNext() || $active_page.next().length) {
        $next_page_trigger.removeClass('is-disabled');
      } else {
        $next_page_trigger.addClass('is-disabled');
      }
    });
  }

  bindClickPrev() {
    const $next_page_trigger = $(`.${this.nextPageTriggerClass}`);
    const $prev_page_trigger = $(`.${this.prevPageTriggerClass}`);

    $prev_page_trigger.click((e) => {
      e.preventDefault();

      let $active_page = this.$widget.find(`.${this.pageClass}.is-active`);
      let current_position = parseInt(this.$widget.css('left'), 10);

      if ($active_page.prev().length) {
        $active_page = $active_page.removeClass('is-active').prev().addClass('is-active');
        this.$widget.css('left', current_position + this.pageWidth);
      }
      if (!$active_page.prev().length) {
        $prev_page_trigger.addClass('is-disabled');
      }
      if ($active_page.next().length) {
        $next_page_trigger.removeClass('is-disabled');
      }
    });
  }

  bindWindowResize() {
    $WINDOW.resize(() => {
      if (IS_SMALL_MOBILE_WIDTH()) {
        this.options.limit = this.limit.mobile;
      } else {
        this.options.limit = this.limit.default;
      }
    });
  }
}
