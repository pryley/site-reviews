/**
 * A select with data-glsr-track carries its value in that attribute, for the stylesheet to read.
 */
export default class TrackSelect {
    static init (root = document) {
        root.querySelectorAll('select[data-glsr-track]').forEach(select => {
            TrackSelect.track(select)
            select.addEventListener('change', () => TrackSelect.track(select))
        })
    }

    static track (select) {
        select.dataset.glsrTrack = select.value;
    }
}
