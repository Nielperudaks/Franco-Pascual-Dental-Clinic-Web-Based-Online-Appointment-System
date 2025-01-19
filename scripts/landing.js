window.addEventListener("load", event => {
    const arrowNext = document.querySelector(".button-next"),
        arrowPrev = document.querySelector(".button-prev"),
        imageActive = document.querySelector(".active img"),
        imageNext = document.querySelector(".next img"),
        slideActive = document.querySelector(".active"),
        slideNext = document.querySelector(".next");
  
    const images = [{
            id: 0,
            image: "https://th.bing.com/th/id/R.1a1e22a77aaaffd2f1bd919809809c14?rik=r4xqFiXvfIovNA&riu=http%3a%2f%2fkingsleyorthodontics.com.au%2fwp-content%2fuploads%2f2023%2f06%2fCosmetic-Dentistry-Definition-Methods-and-Types-in-Perth.jpg&ehk=AKQCUl1T93zS8kRnLSUSv2xgJbrhjyJ1YNKatK%2bO%2bFc%3d&risl=&pid=ImgRaw&r=0"
        },
        {
            id: 1,
            image: "https://thornhilldental.ca/wp-content/uploads/2020/08/dental-treatment.jpg"
        },
        {
            id: 2,
            image: "https://archimages-stl.com/wp-content/uploads/2020/03/SKYVIEW_05-1024x681.jpg"
        },
        {
            id: 3,
            image: "https://i1.wp.com/dentree.co.za/wp-content/uploads/2018/01/metal-braces-dentalia-demo-1.jpg?fit=1920%2C1280&ssl=1"
        }
    ];
  
    arrowNext.addEventListener("click", nextPhoto);
    arrowPrev.addEventListener("click", prevPhoto);
  
    function nextPhoto() {
        const nextId = ~~imageNext.dataset.id + 1,
            nextPicture = images.find(element => element.id == nextId),
            bullet = document.querySelectorAll(".bullet"),
            activeBullet = [...bullet].find(
                element => element.dataset.id == ~~imageNext.dataset.id
            );
  
  
        // Add Classes to Anime Photos
  
        slideNext.classList.add("anime-next-in");
        slideActive.classList.add("anime-in");
  
        // To Remove the Class that anime in
        setTimeout(function () {
            slideActive.classList.remove("anime-in");
            slideNext.classList.remove("anime-next-in");
        }, 960);
  
        // To Populate the Active and Next Slide
  
        imageActive.src = imageNext.src;
        imageActive.dataset.id = imageNext.dataset.id;
  
        if (imageActive.dataset.id == images.length - 1) {
            imageNext.src = images[0].image;
            imageNext.dataset.id = images[0].id;
        } else {
            imageNext.src = nextPicture.image;
            imageNext.dataset.id = nextPicture.id;
        }
  
        // To Add Active bullets
        bullet.forEach(function (el) {
            el.classList.remove("selected");
        });
        activeBullet.classList.add("selected");
  
    }
  
    function prevPhoto() {
        const prevId = ~~imageActive.dataset.id - 1,
            prevPicture = images.find(element => element.id == prevId),
            bullet = document.querySelectorAll(".bullet");
        let activeBullet = [...bullet].find(
            element => element.dataset.id == prevId
        );
  
        // Add Classes to Anime Photos
  
        slideActive.classList.add("anime-out");
        slideNext.classList.add("anime-next-out");
  
        // To Remove the Class that anime in
        setTimeout(function () {
            slideActive.classList.remove("anime-out");
            slideNext.classList.remove("anime-next-out");
        }, 960);
  
  
        // To Populate the Active and Next Slide
        if (imageActive.dataset.id == 0) {
            imageNext.src = imageActive.src;
            imageNext.dataset.id = imageActive.dataset.id;
            imageActive.src = images[images.length - 1].image;
            imageActive.dataset.id = images[images.length - 1].id;
            activeBullet = [...bullet].find(
                element => element.dataset.id == imageActive.dataset.id
            );
        } else {
            imageNext.src = imageActive.src;
            imageNext.dataset.id = imageActive.dataset.id;
            imageActive.src = prevPicture.image;
            imageActive.dataset.id = prevPicture.id;
        }
  
        // To Add Active bullets
        bullet.forEach(function (el) {
            el.classList.remove("selected");
        });
        activeBullet.classList.add("selected");
    }
  
    // To Populate the first images on page load
    imageActive.src = images[0].image;
    imageActive.dataset.id = images[0].id;
  
    imageNext.src = images[1].image;
    imageNext.dataset.id = images[1].id;
  
  
  
    // Sticky menu 
  
    window.onscroll = function () {
  
        if (window.pageYOffset >= 90) {
            iconMenu.classList.add('sticky');
  
        } else {
            iconMenu.classList.remove('sticky');
        }
    }
    // open menu
  
    const iconMenu = document.querySelector('.icon-menu'),
        menuOverlay = document.querySelector('.main-nav'),
        body = document.querySelector('body');
  
    iconMenu.addEventListener("click", openMenu);
  
    // Sticky menu 
  
    window.onscroll = function () {
  
        if (window.pageYOffset >= 90) {
            iconMenu.classList.add('sticky');
  
        } else {
            iconMenu.classList.remove('sticky');
        }
    }
  
  
    function openMenu() {
  
        if (iconMenu.classList.contains('icon-open')) {
            menuOverlay.classList.add('menu-close');
            iconMenu.classList.remove('icon-open');
            body.classList.remove('no-scroll');
            iconMenu.classList.remove('sticky');
  
            setTimeout(function () {
                menuOverlay.classList.remove('menu-open');
            }, 800);
            setTimeout(function () {
                menuOverlay.classList.remove('menu-close');
            }, 900);
  
        } else {
            menuOverlay.classList.remove('menu-close');
            menuOverlay.classList.add('menu-open');
            iconMenu.classList.add('icon-open');
            body.classList.add('no-scroll');
  
            setTimeout(function () {
                iconMenu.classList.remove('sticky');
            }, 500);
  
        }
    }
  
    // open modal video
  
    const videoItem = document.querySelectorAll('.video-item'),
        modalVideo = document.querySelector('.modal-video'),
        iconCloseVideo = document.querySelector('.close-video'),
        videoFrame = document.querySelector(".video-code iframe"),
        videoOpen = document.querySelector(".video-open");
  
  
  
    videoItem.forEach(function (el) {
        el.addEventListener("click", openVideo);
    })
  
    iconCloseVideo.addEventListener("click", closeVideo);
    // body.addEventListener("click", closeVideo);
  
  
    function openVideo(e) {
        const videoSrc = e.currentTarget.dataset.video;
  
        body.classList.add('no-scroll');
  
        modalVideo.classList.add('video-open');
  
        videoFrame.src = videoSrc;
  
    }
  
    // ytLink.href = videoUrl;
  
  
  
    function closeVideo() {
  
        body.classList.remove('no-scroll');
        modalVideo.classList.remove('video-open');
        videoFrame.src = "";
  
    }
  
  
  });
  
  let btnBack = document.querySelector('button');
  btnBack.addEventListener('click', () => {
    window.history.back();
  })

  function showSidebar(){
    const sidebar = document.querySelector('.sidebar')
    sidebar.style.display='flex'
  }

  function hideSidebar(){
    const sidebar = document.querySelector('.sidebar')
    sidebar.style.display='none'
  }



  document.querySelectorAll('nav a').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        if (this.getAttribute('href') !== 'https://fpdentalclinic.com/Patient_Login/') {
            e.preventDefault();
            document.querySelector(this.getAttribute('href')).scrollIntoView({
                behavior: 'smooth'
            });
            document.querySelectorAll('nav a').forEach(link => {
                link.classList.remove('nav-clicked');
            });
            this.classList.add('nav-clicked');
        }
    });
});
