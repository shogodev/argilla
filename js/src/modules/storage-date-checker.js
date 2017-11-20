export default class StorageDateChecker {
  constructor(options) {
    this.storage_key = options.storageKey;
    this.hide_period = 1000 * 60 * 60 * 24 * options.daysToHide;

    this.hide_date = this.getHideDate();
    this.show_date = this.hide_date * 1 + this.hide_period;
  }

  getHideDate() {
    return localStorage.getItem(this.storage_key);
  }

  setHideDate(date) {
    localStorage.setItem(this.storage_key, date);
  }

  itsTimeToShow() {
    let current_date = new Date().getTime();

    if (!this.hide_date || current_date > this.show_date) {
      return true;
    } else {
      return false;
    }
  }
}
