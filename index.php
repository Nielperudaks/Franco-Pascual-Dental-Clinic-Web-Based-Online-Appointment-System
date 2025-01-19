<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Franco-Pascual Dental Clinic</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <script>
    $(document).ready(function() {      
        function fetchServices() {
            $.ajax({
                url: 'fetch-services.php', 
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    
                    populateServices(response);
                    createSlideIndicators(response.length);
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching services:', error);
                }
            });
        }     
        function populateServices(services) {
            $('#services-slider').empty(); 
            services.forEach((service, index) => {
                let slideNumber = (index + 1).toString().padStart(2, '0');             
                let slideHtml = `
                    <div class="w-full flex-shrink-0 px-5">
                        <div class="bg-white p-8 rounded-xl shadow-[0_4px_20px_rgba(0,0,0,0.08)] hover:shadow-[0_8px_30px_rgba(0,0,0,0.12)] border-2 border-blue-100 hover:border-blue-500 transition-all duration-500 ease-in-out h-[450px] flex flex-col">
                            <div class="flex items-center mb-6">
                                <span class="text-3xl text-blue-600 mr-4">${slideNumber}</span>
                                <h3 class="text-2xl font-playfair font-semibold text-blue-800">${service.ServiceName}</h3>
                            </div>
                            <p class="text-gray-600 text-2xl mb-6 flex-grow">${service.Description}</p>
                            <p class="service-price text-lg font-bold text-blue-600 mt-4">₱${parseFloat(service.Price).toFixed(2)}</p> 
                        </div>
                    </div>
                    `;

                
                $('#services-slider').append(slideHtml);
            });
        }
        function createSlideIndicators(serviceCount) {
            $('#slide-indicators').empty(); 

            for (let i = 0; i < serviceCount; i++) {
                let indicatorHtml = `
                <button class="w-3 h-3 rounded-full hover:bg-blue-400 transition-colors bg-blue-200"></button>
            `;               
                $('#slide-indicators').append(indicatorHtml);
            }
        }      
        fetchServices();
    });
    </script>

    <script>
    
    $(document).ready(function() {
        
        function checkScroll() {
            $('.fade-section').each(function() {
                var $elem = $(this);
                var topOfWindow = $(window).scrollTop();
                var bottomOfWindow = topOfWindow + $(window).height();
                var topOfElement = $elem.offset().top;
                var bottomOfElement = topOfElement + $elem.height();

                
                if ((bottomOfWindow >= topOfElement) && (topOfWindow <= bottomOfElement)) {
                    if (!$elem.hasClass('is-visible')) {
                        $elem.addClass('is-visible');
                    }
                }
            });
        }

        
        $(window).on('scroll resize', checkScroll);
        checkScroll();

        
        $('.feature-highlight').hover(
            function() {
                
                $(this).addClass('transform scale-105 shadow-xl');
                $(this).find('svg').addClass('text-blue-700');
            },
            function() {
                
                $(this).removeClass('transform scale-105 shadow-xl');
                $(this).find('svg').removeClass('text-blue-700');
            }
        );

        
        let currentSlide = 0;
        const $slider = $('#services-slider');
        const $slides = $slider.find('> div');
        const totalSlides = $slides.length;

        
        const $indicators = $('#slide-indicators');
        $slides.each(function(index) {
            $indicators.append(`
            <button class="w-3 h-3 rounded-full bg-blue-300 slide-indicator ${index === 0 ? 'active' : ''}" 
                    data-slide="${index}"></button>
        `);
        });

        
        function updateSlide(newSlide) {
            
            newSlide = Math.max(0, Math.min(newSlide, totalSlides - 1));

            
            const translateX = -newSlide * 100;
            $slider.css('transform', `translateX(${translateX}%)`);

            
            $('.slide-indicator').removeClass('active bg-blue-600').addClass('bg-blue-300');
            $(`.slide-indicator[data-slide="${newSlide}"]`)
                .removeClass('bg-blue-300')
                .addClass('active bg-blue-600');

            currentSlide = newSlide;
        }

        
        $('#next-slide').on('click', function() {
            updateSlide(currentSlide + 1);
        });

        
        $('#prev-slide').on('click', function() {
            updateSlide(currentSlide - 1);
        });

        
        $('.slide-indicator').on('click', function() {
            updateSlide($(this).data('slide'));
        });

        
        $('.cta-button').hover(
            function() {
                $(this).addClass('transform -translate-y-1 shadow-xl');
            },
            function() {
                $(this).removeClass('transform -translate-y-1 shadow-xl');
            }
        );

        
        $('a[href^="#"]').on('click', function(event) {
            var target = $(this.getAttribute('href'));
            if (target.length) {
                event.preventDefault();
                $('html, body').stop().animate({
                    scrollTop: target.offset().top
                }, 800);
            }
        });
    });
    </script>
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Inter:wght@300;400;500;600&display=swap"
        rel="stylesheet">
    <style>
    body {
        font-family: 'Montserrat', sans-serif;
    }

    .gradient-bg {
        background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
    }

    .soft-shadow {
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }

    .hover-lift {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .hover-lift:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
    }

    .transition-all {
        transition-property: all;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        transition-duration: 500ms;
    }

    .hover\:shadow-lg:hover {
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        transition-duration: 500ms;
    }

    a,
    button {
        transition: all 300ms cubic-bezier(0.4, 0, 0.2, 1);
    }

    img {
        transition: all 500ms cubic-bezier(0.4, 0, 0.2, 1);
    }
    .fade-section {
        opacity: 0;
        transform: translateY(20px);
        transition: opacity 0.6s ease-out, transform 0.6s ease-out;
    }
    .fade-section.is-visible {
        opacity: 1;
        transform: translateY(0);
    }
    </style>
</head>

<body class="bg-blue-50 font-inter">
    <?php include 'components/navbar.php'; ?>

    <main>
        
        <section class="relative min-h-screen flex items-center py-20 md:py-0 fade-section">
            
            <div class="absolute inset-0 z-0">
                <img src="res/1.png" alt="Dental Clinic" class="w-full h-full object-cover">
                
                <div class="absolute inset-0 bg-gradient-to-r from-blue-900/75 to-blue-800/75 backdrop-brightness-50">
                </div>
            </div>

            
            <div class="relative z-10 container mx-auto px-4">
                <div class="max-w-4xl mx-auto">
                    
                    <div class="text-center mb-8 md:mb-12 pt-20 md:pt-0">
                        <h1
                            class="text-2xl md:text-6xl font-playfair font-bold text-white mb-4 md:mb-6 leading-tight drop-shadow-lg">
                            Welcome to
                            <span class="text-blue-200 drop-shadow-lg block mt-2 text-7xl">Franco-Pascual</span>
                            <span class="block mt-2 drop-shadow-lg">Dental Clinic</span>
                        </h1>
                        <p
                            class="text-lg md:text-xl text-white mb-8 md:mb-12 leading-relaxed max-w-2xl mx-auto drop-shadow-md font-medium px-4">
                            Experience excellence in dental care with our state-of-the-art facility and expert team
                            dedicated to your perfect smile.
                        </p>

                        
                        <div class="flex flex-col sm:flex-row justify-center gap-4 px-4">
                            <a href="https://fpdentalclinic.com/Patient_Login/"
                                class="bg-blue-600 text-white px-6 md:px-8 py-3 md:py-4 rounded-lg hover:bg-blue-700 transition-all duration-300 transform hover:-translate-y-1 shadow-lg hover:shadow-xl text-base md:text-lg font-medium inline-flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                Book Appointment
                            </a>
                            <a href="#contact"
                                class="bg-white/20 backdrop-blur-sm text-white px-6 md:px-8 py-3 md:py-4 rounded-lg hover:bg-white/30 transition-all duration-300 transform hover:-translate-y-1 shadow-lg hover:shadow-xl text-base md:text-lg font-medium inline-flex items-center justify-center border border-white/40">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                Contact Us
                            </a>
                        </div>
                    </div>

                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6 mt-8 md:mt-16 px-4">
                        <div class="bg-white/20 backdrop-blur-sm p-4 md:p-6 rounded-lg border border-white/40">
                            <div class="text-white mb-3">
                                <svg class="w-6 h-6 md:w-8 md:h-8" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                            </div>
                            <h3 class="text-white text-base md:text-lg font-semibold mb-2 drop-shadow-md">Expert Care
                            </h3>
                            <p class="text-white/90 drop-shadow-md text-sm md:text-base">Professional team with years of
                                experience</p>
                        </div>
                        <div class="bg-white/20 backdrop-blur-sm p-4 md:p-6 rounded-lg border border-white/40">
                            <div class="text-white mb-3">
                                <svg class="w-6 h-6 md:w-8 md:h-8" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <h3 class="text-white text-base md:text-lg font-semibold mb-2 drop-shadow-md">Modern
                                Facility</h3>
                            <p class="text-white/90 drop-shadow-md text-sm md:text-base">State-of-the-art equipment and
                                technology</p>
                        </div>
                        <div class="bg-white/20 backdrop-blur-sm p-4 md:p-6 rounded-lg border border-white/40">
                            <div class="text-white mb-3">
                                <svg class="w-6 h-6 md:w-8 md:h-8" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h3 class="text-white text-base md:text-lg font-semibold mb-2 drop-shadow-md">Convenient
                                Hours</h3>
                            <p class="text-white/90 drop-shadow-md text-sm md:text-base">Flexible scheduling to fit your
                                needs</p>
                        </div>
                    </div>
                </div>
            </div>

            

        </section>

        
        <section id="dental-services" class="bg-white py-16 fade-section">
            <div class="container mx-auto px-4">
                <div class="max-w-3xl mx-auto text-center mb-16">
                    <h2 class="text-3xl font-playfair font-bold text-blue-900 mb-4 text-center">Our Dental Services
                    </h2>
                    <p class="text-gray-600 text-lg">Choose from the variety of quality dental services</p>
                </div>


                <div class="flex flex-col lg:flex-row gap-12">
                    
                    <div class="lg:w-1/2">
                        <div class="rounded-lg overflow-hidden shadow-lg h-[500px]">

                            <img src="res/2.png" alt="Dental Services" class="w-full h-full object-cover">
                        </div>
                    </div>

                    
                    <div class="lg:w-1/2">
                        <div class="relative">
                            <div class="overflow-hidden">
                                <div id="services-slider" class="flex transition-transform duration-500 ease-out -mx-5">
                                    
                                    <div class="w-full flex-shrink-0 px-5">
                                        <div
                                            class="bg-white p-8 rounded-xl shadow-[0_4px_20px_rgba(0,0,0,0.08)] hover:shadow-[0_8px_30px_rgba(0,0,0,0.12)] border-2 border-blue-100 hover:border-blue-500 transition-all duration-500 ease-in-out h-[450px] flex flex-col">

                                            <div class="flex items-center mb-6">
                                                <span class="text-3xl text-blue-600 mr-4">01</span>
                                                <h3 class="text-2xl font-playfair font-semibold text-blue-800">
                                                    Orthodontics</h3>
                                            </div>
                                            <p class="text-gray-600 mb-6 flex-grow">Expert orthodontic care for
                                                perfectly aligned smiles. We offer comprehensive orthodontic solutions.
                                            </p>
                                            <ul class="space-y-3">
                                                <li class="flex items-center text-gray-600">
                                                    <svg class="w-5 h-5 mr-3 text-blue-500 flex-shrink-0"
                                                        fill="currentColor" viewBox="0 0 20 20">
                                                        <path
                                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z">
                                                        </path>
                                                    </svg>
                                                    <span>Braces Installation</span>
                                                </li>
                                                <li class="flex items-center text-gray-600">
                                                    <svg class="w-5 h-5 mr-3 text-blue-500 flex-shrink-0"
                                                        fill="currentColor" viewBox="0 0 20 20">
                                                        <path
                                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z">
                                                        </path>
                                                    </svg>
                                                    <span>Invisalign Treatment</span>
                                                </li>
                                                <li class="flex items-center text-gray-600">
                                                    <svg class="w-5 h-5 mr-3 text-blue-500 flex-shrink-0"
                                                        fill="currentColor" viewBox="0 0 20 20">
                                                        <path
                                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z">
                                                        </path>
                                                    </svg>
                                                    <span>Retainers & Maintenance</span>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>

                                    
                                    <div class="w-full flex-shrink-0 px-5">
                                        <div
                                            class="bg-white p-8 rounded-xl shadow-[0_4px_20px_rgba(0,0,0,0.08)] hover:shadow-[0_8px_30px_rgba(0,0,0,0.12)] border-2 border-blue-100 hover:border-blue-500 transition-all duration-500 ease-in-out h-[450px] flex flex-col">
                                            <div class="flex items-center mb-6">
                                                <span class="text-3xl text-blue-600 mr-4">02</span>
                                                <h3 class="text-2xl font-playfair font-semibold text-blue-800">
                                                    Periodontics</h3>
                                            </div>
                                            <p class="text-gray-600 mb-6 flex-grow">Comprehensive gum care and treatment
                                                for optimal oral health.</p>
                                            <ul class="space-y-3">
                                                <li class="flex items-center text-gray-600">
                                                    <svg class="w-5 h-5 mr-3 text-blue-500 flex-shrink-0"
                                                        fill="currentColor" viewBox="0 0 20 20">
                                                        <path
                                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z">
                                                        </path>
                                                    </svg>
                                                    <span>Deep Cleaning</span>
                                                </li>
                                                <li class="flex items-center text-gray-600">
                                                    <svg class="w-5 h-5 mr-3 text-blue-500 flex-shrink-0"
                                                        fill="currentColor" viewBox="0 0 20 20">
                                                        <path
                                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z">
                                                        </path>
                                                    </svg>
                                                    <span>Gum Surgery</span>
                                                </li>
                                                <li class="flex items-center text-gray-600">
                                                    <svg class="w-5 h-5 mr-3 text-blue-500 flex-shrink-0"
                                                        fill="currentColor" viewBox="0 0 20 20">
                                                        <path
                                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z">
                                                        </path>
                                                    </svg>
                                                    <span>Periodontal Maintenance</span>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>

                                    


                                </div>
                            </div>

                            
                            <div class="flex justify-between items-center mt-8">
                                <div class="flex space-x-2">
                                    <button id="prev-slide"
                                        class="bg-blue-600 text-white w-10 h-10 rounded-full flex items-center justify-center hover:bg-blue-700 transition-colors">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 19l-7-7 7-7" />
                                        </svg>
                                    </button>
                                    <button id="next-slide"
                                        class="bg-blue-600 text-white w-10 h-10 rounded-full flex items-center justify-center hover:bg-blue-700 transition-colors">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5l7 7-7 7" />
                                        </svg>
                                    </button>
                                </div>
                                <div id="slide-indicators" class="flex space-x-2">
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>



        
        <section id="dental-details" class="py-20 bg-blue-50 fade-section">
            <div class="container mx-auto px-4">
                <div class="max-w-3xl mx-auto text-center mb-16">
                    <h2 class="text-3xl md:text-4xl font-playfair font-bold text-blue-900 mb-4">Why Choose Us</h2>
                    <p class="text-gray-600 text-lg">Experience the perfect blend of modern technology and compassionate
                        care</p>
                </div>

                <div class="grid lg:grid-cols-2 gap-16 items-center">
                    
                    <div class="space-y-12">
                        
                        <div
                            class="bg-white p-8 rounded-xl shadow-[0_4px_20px_rgba(0,0,0,0.08)] hover:shadow-[0_8px_30px_rgba(0,0,0,0.12)] transition-all duration-500 ease-in-out transform hover:-translate-y-1">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <div class="bg-blue-100 rounded-lg p-3">
                                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-6">
                                    <h3 class="text-xl font-playfair font-semibold text-blue-900 mb-3">State-of-the-Art
                                        Technology</h3>
                                    <p class="text-gray-600 leading-relaxed">Our clinic is equipped with the latest
                                        dental technology, including digital X-rays and 3D scanning, ensuring precise
                                        diagnostics and treatment planning.</p>
                                </div>
                            </div>
                        </div>

                        
                        <div
                            class="bg-white p-8 rounded-xl shadow-[0_4px_20px_rgba(0,0,0,0.08)] hover:shadow-[0_8px_30px_rgba(0,0,0,0.12)] transition-all duration-500 ease-in-out transform hover:-translate-y-1">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <div class="bg-blue-100 rounded-lg p-3">
                                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-6">
                                    <h3 class="text-xl font-playfair font-semibold text-blue-900 mb-3">Expert Dental
                                        Team</h3>
                                    <p class="text-gray-600 leading-relaxed">Our experienced dentists and staff are
                                        committed to providing exceptional care with a gentle touch and personalized
                                        attention.</p>
                                </div>
                            </div>
                        </div>

                        
                        <div
                            class="bg-white p-8 rounded-xl shadow-[0_4px_20px_rgba(0,0,0,0.08)] hover:shadow-[0_8px_30px_rgba(0,0,0,0.12)] transition-all duration-500 ease-in-out transform hover:-translate-y-1">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <div class="bg-blue-100 rounded-lg p-3">
                                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-6">
                                    <h3 class="text-xl font-playfair font-semibold text-blue-900 mb-3">Patient-First
                                        Approach</h3>
                                    <p class="text-gray-600 leading-relaxed">Enjoy a comfortable environment with modern
                                        amenities, designed to make your dental visit as relaxing as possible.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    
                    <div class="relative">
                        <div class="rounded-2xl overflow-hidden shadow-2xl">
                            <img src="res/3.png" alt="Dental Facility" class="w-full h-[600px] object-cover">
                            <div class="absolute inset-0 bg-gradient-to-t from-blue-900/20 to-transparent"></div>
                        </div>
                        
                        <div class="absolute bottom-8 left-8 right-8">
                            <div class="bg-white/90 backdrop-blur-sm rounded-xl p-6 grid grid-cols-3 gap-8">
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-blue-900">15+</div>
                                    <div class="text-sm text-gray-600">Years Experience</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-blue-900">5000+</div>
                                    <div class="text-sm text-gray-600">Happy Patients</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-blue-900">99%</div>
                                    <div class="text-sm text-gray-600">Satisfaction</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section id="dental-services" class="bg-white py-16 min-h-screen flex items-center justify-center">
            <div class="container mx-auto px-4 max-w-[1540px] w-[100%]">
                <h2 class="text-3xl font-playfair font-bold text-blue-900 mb-12 text-center">The Human Teeth</h2>

                <div class="flex flex-col lg:flex-row gap-12 justify-center items-center">
                    
                    <div class="lg:w-3/4 w-full">
                        
                        <div class="relative w-full overflow-hidden rounded-lg shadow-lg"
                            style="padding-bottom: 56.25%;">
                            <iframe id="embedded-human" class="absolute top-0 left-0 w-full h-full" frameborder="0"
                                allowfullscreen="true" loading="lazy" style="aspect-ratio: 16 / 9;"
                                src="https://human.biodigital.com/viewer/?id=5q0h&ui-anatomy-descriptions=true&ui-anatomy-pronunciations=true&ui-anatomy-labels=true&ui-audio=true&ui-chapter-list=false&ui-fullscreen=true&ui-help=true&ui-info=true&ui-label-list=true&ui-layers=true&ui-skin-layers=true&ui-loader=circle&ui-media-controls=full&ui-menu=true&ui-nav=true&ui-search=true&ui-tools=true&ui-tutorial=false&ui-undo=true&ui-whiteboard=true&initial.none=true&disable-scroll=false&uaid=LyfVl&paid=o_1af734d5">
                            </iframe>
                        </div>


                    </div>
                </div>
            </div>
        </section>

        
        <section id="contact" class="py-20 bg-white relative">
            
            <div class="absolute inset-0 bg-blue-50 opacity-50 pointer-events-none">
                <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\" 20\"
                    height=\"20\" viewBox=\"0 0 20 20\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"%239C92AC\"
                    fill-opacity=\"0.1\" fill-rule=\"evenodd\"%3E%3Ccircle cx=\"3\" cy=\"3\" r=\"3\"/%3E%3Ccircle
                    cx=\"13\" cy=\"13\" r=\"3\"/%3E%3C/g%3E%3C/svg%3E');"></div>
            </div>

            <div class="container mx-auto px-4 relative">
                <div class="max-w-3xl mx-auto text-center mb-16">
                    <h2 class="text-3xl md:text-4xl font-playfair font-bold text-blue-900 mb-4">Get in Touch</h2>
                    <p class="text-gray-600 text-lg">Schedule your appointment or ask us any questions</p>
                </div>

                <div class="grid md:grid-cols-1 gap-12 max-w-7xl mx-auto justify-center items-center">
                    

                    
                    <div class="space-y-10 px-4 md:px-0">
                        
                        <div class="bg-white rounded-xl h-[350px] overflow-hidden shadow-[0_4px_20px_rgba(0,0,0,0.08)]">
                            <iframe
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3863.515962334644!2d121.04780238585127!3d14.455036149953083!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397cfc09b9e7cff%3A0x44cbc26733bfd8df!2s570%20Purok%203%2C%20Muntinlupa%2C%201770%20Metro%20Manila!5e0!3m2!1sen!2sph!4v1732313226579!5m2!1sen!2sph"
                                width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>

                        
                        <div class="grid gap-8">
                            <div
                                class="bg-white p-8 rounded-xl shadow-[0_4px_20px_rgba(0,0,0,0.08)] hover:shadow-[0_8px_30px_rgba(0,0,0,0.12)] transition-all duration-500">
                                <div class="flex items-start">
                                    <div class="bg-blue-100 rounded-lg p-3">
                                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <h4 class="text-lg font-semibold text-blue-900">Location</h4>
                                        <p class="text-gray-600 mt-1">570 Purok 3, Gate 1<br />Sucat, Muntinlupa City
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </section>
    </main>

    <?php include 'components/footer.php'; ?>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const slider = document.getElementById('services-slider');
        const slides = slider.children;
        const prevButton = document.getElementById('prev-slide');
        const nextButton = document.getElementById('next-slide');
        const indicators = document.getElementById('slide-indicators');

        let currentSlide = 0;

        for (let i = 0; i < slides.length; i++) {
            const dot = document.createElement('button');
            dot.classList.add('w-3', 'h-3', 'rounded-full', 'bg-blue-200', 'hover:bg-blue-400',
                'transition-colors');
            if (i === 0) dot.classList.add('bg-blue-600');
            dot.addEventListener('click', () => goToSlide(i));
            indicators.appendChild(dot);
        }

        function updateSlider() {
            slider.style.transition = 'transform 800ms cubic-bezier(0.4, 0, 0.2, 1)';
            slider.style.transform = `translateX(-${currentSlide * 100}%)`;

            const dots = indicators.children;
            for (let i = 0; i < dots.length; i++) {
                dots[i].classList.toggle('bg-blue-600', i === currentSlide);
                dots[i].classList.toggle('bg-blue-200', i !== currentSlide);
            }
        }

        function nextSlide() {
            currentSlide = (currentSlide + 1) % slides.length;
            updateSlider();
        }

        function prevSlide() {
            currentSlide = (currentSlide - 1 + slides.length) % slides.length;
            updateSlider();
        }

        function goToSlide(index) {
            currentSlide = index;
            updateSlider();
        }

        prevButton.addEventListener('click', prevSlide);
        nextButton.addEventListener('click', nextSlide);

        setInterval(nextSlide, 5000);

        slider.addEventListener('transitionend', function() {
            slider.style.transition = 'none';
        });
    });
    </script>
</body>

</html>

</footer>

</body>

</html>