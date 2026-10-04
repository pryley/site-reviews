/**
 * A row of tabs that wraps gets the class `is-wrapped`, which styles its tabs as separate buttons.
 */
const watch = (navEl) => {
    let width = null;
    const update = () => {
        if (width === navEl.clientWidth) return;
        width = navEl.clientWidth;
        // measured as tabs: the buttons have margins of their own
        navEl.classList.remove('is-wrapped')
        const tabs = [...navEl.querySelectorAll('.glsr-nav-tab')];
        navEl.classList.toggle('is-wrapped', tabs.some(tab => tab.offsetTop !== tabs[0].offsetTop))
    }
    new ResizeObserver(update).observe(navEl)
    update()
}

export default () => document.querySelectorAll('.glsr-nav-tab-wrapper').forEach(watch)
