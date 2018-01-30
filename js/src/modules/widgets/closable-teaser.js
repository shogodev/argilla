/**
 * markup example:
 *
 * <div class="js-floating-teaser"> <!-- should be hidden by css initially -->
 *   <div class="js-close-floating-teaser"></div>
 * </div>
 *
 * initialization example:
 *
 * const teaser = new ClosableTeaser({
 *   selector: '.js-floating-teaser',
 *   closeSelector: '.js-close-floating-teaser',
 *   storageKey: 'TEASER_HIDE_DATE',
 *   daysToHide: 7,
 * });
 *
 */

import StorageDateChecker from './shared/storage-date-checker';

export default class ClosableTeaser extends StorageDateChecker {
  constructor(options) {
    super({
      storageKey: options.storageKey,
      daysToHide: options.daysToHide,
    });

    this.$teaser = $(options.selector);
    this.$close_trigger = $(options.closeSelector);
    this.init();
  }

  init() {
    if (this.$teaser.length && this.itsTimeToShow()) {
      this.show();
      this.$close_trigger.click(() => {
        this.hide();
      });
    }
  }

  show() {
    this.$teaser.stop(true, true).fadeIn();
  }

  hide() {
    this.$teaser.stop(true, true).fadeOut();
    this.setHideDate(new Date().getTime());
  }
}
