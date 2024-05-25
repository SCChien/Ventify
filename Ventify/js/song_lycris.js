document.addEventListener("DOMContentLoaded", function() {
    // Add click event listener to ft_left div
    document.querySelector('.icon-geciweidianji').addEventListener('click', function() {
        var lyricsSection = document.querySelector('.lyrics-section');
        // Toggle visibility of the lyrics section
        if (lyricsSection.classList.contains('slide-in')) {
            // If lyrics section is visible, slide out
            lyricsSection.classList.remove('slide-in');
            lyricsSection.classList.add('slide-out');
            // Hide lyrics section after animation completes
            setTimeout(function() {
                lyricsSection.style.display = 'none';
            }, 500); // Animation duration is 0.5s
        } else {
            // If lyrics section is hidden, fetch and slide in
            fetchLyrics().then(function(lyrics) {
                // Display lyrics
                document.querySelector('.lyrics').innerText = lyrics;
                lyricsSection.style.display = 'block';
                lyricsSection.classList.remove('slide-out');
                lyricsSection.classList.add('slide-in');
            }).catch(function(error) {
                console.error('Error fetching lyrics:', error);
            });
        }
    });

    // Function to fetch lyrics (replace this with your actual implementation)
    function fetchLyrics() {
        // This is just a placeholder function. Replace it with your logic.
        // You may need to pass song information to your server to fetch the correct lyrics.
        // For simplicity, I'm just returning dummy lyrics here.
        return new Promise(function(resolve, reject) {
            var lyrics = "These are the song lyrics.";
            resolve(lyrics);
        });
    }
});
