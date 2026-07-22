document.addEventListener("DOMContentLoaded", () => {
    const viewMoreBtn = document.getElementById("viewMoreBtn");
    
    if (viewMoreBtn) {
        viewMoreBtn.addEventListener("click", () => {
            // Select all hidden gallery items
            const hiddenItems = document.querySelectorAll(".hidden-gallery-item");
            
            hiddenItems.forEach(item => {
                item.style.display = "block"; // Make them visible
            });
            
            // Hide the button once all images are shown
            viewMoreBtn.style.display = "none";
        });
    }
});