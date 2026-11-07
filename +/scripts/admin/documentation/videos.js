/**
 * The videos of the Getting Started page: a playlist item or the play button loads its video frame.
 */
export default class Videos {
    static init (root = document) {
        root.querySelectorAll('.glsr-videos__playlist a').forEach(link => link.addEventListener('click', Videos.onPlaylistClick))
        root.querySelectorAll('.glsr-youtube-button').forEach(button => button.addEventListener('click', ev => Videos.load(ev.currentTarget)))
    }

    static load (link) {
        const { id } = link.dataset;
        const iframe = document.createElement('iframe');
        iframe.setAttribute('frameborder', '0')
        iframe.setAttribute('allowfullscreen', '')
        iframe.setAttribute('allow', 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture')
        iframe.src = id.length > 12
            ? `https://www.youtube-nocookie.com/embed/videoseries?list=${id}&rel=0&showinfo=0&autoplay=1&modestbranding=1`
            : `https://www.youtube-nocookie.com/embed/${id}?rel=0&showinfo=0&autoplay=1&modestbranding=1`;
        const frame = link.closest('.glsr-videos')?.querySelector('.glsr-youtube');
        if (!frame) return;
        frame.querySelectorAll('iframe').forEach(el => el.remove())
        frame.prepend(iframe)
    }

    static onPlaylistClick (ev) {
        const link = ev.currentTarget;
        const playlist = link.closest('.glsr-videos__playlist');
        const { transform } = window.getComputedStyle(playlist);
        ev.preventDefault()
        if (!['none', 'matrix(1, 0, 0, 1, 0, 0)'].includes(transform)) return; // a collapsed playlist expands on touch without playing
        playlist.querySelectorAll('a').forEach(el => el.classList.remove('is-active'))
        link.classList.add('is-active')
        Videos.load(link)
    }
}
