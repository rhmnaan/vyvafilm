document.addEventListener('DOMContentLoaded', function() {
    const synopsisHeader = document.getElementById('synopsisHeader');
    const synopsisContent = document.getElementById('synopsisContent');
    const readMoreBtn = document.getElementById('readMoreBtn');
    const fullSynopsisText = document.querySelector('.full-synopsis-text');
    const shortSynopsisText = document.getElementById('shortSynopsisText');
    const chevronIcon = synopsisHeader.querySelector('i');

    // Ambil nilai needs_truncation dari data attribute
    const needsTruncation = synopsisHeader.dataset.needsTruncation === 'true';

    function toggleSynopsis() {
        synopsisContent.classList.toggle('expanded');
        if (synopsisContent.classList.contains('expanded')) {
            if (fullSynopsisText) fullSynopsisText.style.display = 'block';
            if (shortSynopsisText) shortSynopsisText.style.display = 'none';
            if (readMoreBtn) readMoreBtn.style.display = 'none';
            chevronIcon.classList.remove('fa-chevron-down');
            chevronIcon.classList.add('fa-chevron-up');
        } else {
            if (fullSynopsisText) fullSynopsisText.style.display = 'none';
            if (shortSynopsisText) shortSynopsisText.style.display = 'block';
            if (readMoreBtn && needsTruncation) readMoreBtn.style.display = 'inline';
            chevronIcon.classList.remove('fa-chevron-up');
            chevronIcon.classList.add('fa-chevron-down');
        }
    }

    synopsisHeader.addEventListener('click', toggleSynopsis);

    if (readMoreBtn) {
        readMoreBtn.addEventListener('click', function(event) {
            event.stopPropagation();
            toggleSynopsis();
        });
    }
});
