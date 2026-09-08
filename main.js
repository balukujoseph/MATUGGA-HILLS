// Wait until the HTML document has been loaded before looking for page elements.
document.addEventListener("DOMContentLoaded", () => {
    // Select the homepage hero that displays the rotating background images.
    const hero = document.querySelector(".hero");
    // Keep all slideshow image paths in one ordered list.
    const heroImages = [
        "assets/images/s6 pic.JPG",
        "assets/images/mixed  O&A.JPG",
        "assets/images/opoka teaching.jpg",
        "assets/images/Volley ball team.JPG",
        "assets/images/students in lab.JPG",
        "assets/images/it lab.JPG"
    ];
    // Define how long each image stays visible and how long the slide takes.
    const slideTime = 5000;
    const transitionTime = 900;
    // Find the optional button that reveals hidden gallery items.
    const viewMoreBtn = document.getElementById("viewMoreBtn");

    // Start the slideshow only when a hero exists and motion is allowed by the visitor's device.
    if (hero && !window.matchMedia("(prefers-reduced-motion: reduce)").matches) {
        // Display the first image immediately when the page opens.
        hero.style.setProperty("--hero-image", `url("${heroImages[0]}")`);
        // Load the remaining images early so later transitions are smooth.
        heroImages.slice(1).forEach(imagePath => {
            // Create a browser image object without adding it to the visible page.
            const image = new Image();
            // Request the image so the browser stores it in its cache.
            image.src = imagePath;
        });

        // Track which image is currently displayed.
        let currentImage = 0;
        // Schedule a new slide after each display interval.
        setInterval(() => {
            // Calculate the next image and place it on the incoming layer.
            const nextImage = (currentImage + 1) % heroImages.length;
            hero.style.setProperty("--hero-next-image", `url("${heroImages[nextImage]}")`);
            // Add the CSS class that moves the current image out and the next image in.
            hero.classList.add("is-changing");

            // Wait for the CSS movement to finish before updating the current image.
            setTimeout(() => {
                // Store the new image as the current slide.
                currentImage = nextImage;
                // Move the completed slide into the current image layer.
                hero.style.setProperty("--hero-image", `url("${heroImages[currentImage]}")`);
                // Remove the movement class so the hero is ready for the next slide.
                hero.classList.remove("is-changing");
            }, transitionTime);
        }, slideTime);
    }
    
    // Add the gallery reveal behavior only when that button exists on the current page.
    if (viewMoreBtn) {
        // Listen for a click on the gallery reveal button.
        viewMoreBtn.addEventListener("click", () => {
            // Select every gallery item that is currently hidden.
            const hiddenItems = document.querySelectorAll(".hidden-gallery-item");
            
            // Make each hidden gallery item visible.
            hiddenItems.forEach(item => {
                item.style.display = "block";
            });
            
            // Hide the button because there are no more hidden items to reveal.
            viewMoreBtn.style.display = "none";
        });
    }
});