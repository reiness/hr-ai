<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sphinx.ai</title>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="icon" href="{{ asset('sphinx1.ico') }}" type="image/x-icon">
    <style>
      @import url('https://fonts.googleapis.com/css2?family=Sora:wght@100..800&display=swap');

      .gradient-text {
            background: linear-gradient(to bottom, rgb(255, 88, 90), rgb(168, 27, 189));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

      .gradient-btn{
        background: linear-gradient(to right, rgb(255, 88, 90), rgb(168, 27, 189));
      }
    </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.9.1/gsap.min.js"></script>
</head>
<body class="font-sora">
<nav id="navbar" class="bg-white p-3 fixed w-full top-0 z-50 transition-shadow duration-300 rounded-none">
    <div class="container mx-auto px-4 flex items-center justify-between">
        <div class="flex items-center">
            <img src="assets/img/sphinx1.svg" alt="Sphinx.ai Logo" class="w-12 h-12 mr-1">
            <span class="text-2xl font-bold">Sphinx.ai</span>
        </div>
        <div class="hidden lg:flex items-center space-x-6 border border-gray-400 rounded-full p-2 px-5">
            <a href="#intro" class="text-gray-800 hover:text-pink-500 transition duration-300">About</a>
            <a href="#works" class="text-gray-800 hover:text-pink-500 transition duration-300">How it works?</a>
            <a href="#devs" class="text-gray-800 hover:text-pink-500 transition duration-300">Developers</a>
        </div>
        <div class="hidden lg:flex items-center space-x-4">
            <a href="{{ route('login') }}" class="text-gray-800 hover:text-pink-500 transition duration-300">Login</a>
            <a href="{{ route('register') }}" class="gradient-btn text-white py-2 px-4 rounded-2xl hover:opacity-90 transition duration-300">Register</a>
        </div>
        <div class="lg:hidden z-50">
            <button id="menu-btn" class="text-gray-800 focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
        </div>
    </div>
    <div id="menu" class="hidden lg:hidden">
        <ul class="flex flex-col items-center space-y-4 mt-4">
            <li><a href="#intro" class="text-gray-800 hover:text-pink-500 transition duration-300">About</a></li>
            <li><a href="#works" class="text-gray-800 hover:text-pink-500 transition duration-300">How it works?</a></li>
            <li><a href="#devs" class="text-gray-800 hover:text-pink-500 transition duration-300">Developers</a></li>
            <li><a href="{{ route('login') }}" class="text-gray-800 hover:text-pink-500 transition duration-300">Login</a></li>
            <li><a href="{{ route('register') }}" class="gradient-btn text-white py-2 px-4 rounded-2xl hover:opacity-90 transition duration-300">Register</a></li>
        </ul>
    </div>
</nav>


    <!-- Intro Section -->
    <section class="text-center py-3 min-h-screen flex flex-col justify-center">
      <div class="container mx-auto px-12">
            <h1 class="text-7xl font-bold gradient-text mb-2">Rank <span class="auto-type1"></span> <br>Compare <span class="auto-type2"></span> </br> </h1>
            <h2 class="text-xl font-medium text-gray-600">AI-driven CV ranking and candidate comparison, simplified. Get started for free.</h2>
            <div class="mt-8">
                <a href="{{ route('login') }}" class="gradient-btn text-white py-3 px-6 rounded-2xl shadow-lg hover:opacity-90 transition duration-300">Get started</a>
                <a href="#intro" class="bg-gray-200 ml-4 text-white-500 py-3 px-6 rounded-2xl hover:opacity-90 hover:text-black-600 transition duration-300">Learn more</a>
            </div>
        </div>
    </section>

<!-- Description Section -->
<section id="intro" class="bg-white min-h-screen flex items-center justify-center">
    <div class="container mx-auto py-5 px-12">
        <div class="flex flex-col md:flex-row items-center">
            <div class="md:w-1/2 flex justify-center relative h-80 md:h-auto">
                <img src="assets/img/hr.jpg" alt="Description Image" class="w-full h-full object-cover rounded-2xl">
                <img src="assets/img/sphinx2.svg" alt="Sphinx Logo" class="absolute top-7 right-5 w-16 h-16">
            </div>
            <div class="md:w-1/2 mt-8 md:mt-0 md:pl-12">
                <h2 class="text-4xl font-bold mb-2">We make it easier for recruiters to find top talent <span class="gradient-text"><span class="auto-type3"></span></span></h2>
                <p class="text-xl font-medium text-gray-600 mb-5">We harness the power of machine learning and LLM (Gemini) to simplify your decision-making process.</p>
                <ul class="list-none">
                    <li class="mb-4 p-6 bg-white rounded-2xl shadow-lg transition-transform transform hover:-translate-y-2 hover:rotate-2 hover:shadow-md">
                        <h1 class="text-xl font-bold gradient-text">Enhanced Keyword Matching</h1>
                        <h2 class="text-lg mb-2 font-medium text-gray-600">Leveraging BM25 and LSI models</h2>
                        <p>We utilize advanced BM25 and LSI models to match relevant keywords from your query directly to CVs. This ensures precise and efficient candidate ranking based on keyword relevance.</p>
                    </li>
                    <li class="mb-4 p-6 bg-white rounded-2xl shadow-lg transition-transform transform hover:-translate-y-2 hover:rotate-2 hover:shadow-md">
                        <h1 class="text-xl font-bold gradient-text">Intelligent Candidate Comparison</h1>
                        <h2 class="text-lg mb-2 font-medium text-gray-600">Powered by Gemini API</h2>
                        <p>Our integration with Gemini API enables sophisticated comparison between top candidates. This allows recruiters to make data-driven decisions quickly and confidently.</p>
                    </li>
                </ul>
            </div> 
        </div>
    </div>
</section>



    <!-- How It Works Section -->
    <section id="works" class="bg-white min-h-screen flex flex-col justify-center">
        <div class="container mx-auto px-12">
            <h2 class="text-4xl font-bold text-center mb-12">How Sphinx.ai <span class="gradient-text"><span class="auto-type4"></span></span></h2>
            <img src="assets/img/bagan.svg" alt="bagan Image" class="h-full w-full">
        </div>
    </section>

    <!-- Developers Section -->
    <section id="devs" class="bg-white min-h-screen flex flex-col justify-center">
        <div class="container mx-auto px-12 py-10">
            <h2 class="text-4xl font-bold text-center mb-12">Sphinx.ai <span class="gradient-text"><span class="auto-type5"></span></span></h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="flex flex-col items-center">
                    <img class="transition-transform transform hover:-translate-y-2 hover:rotate-3 mb-1" src="assets/img/dho.png" alt="Developer Image" class="w-11/12 mb-4">
                    <h2 class="text-3xl font-bold">Ridho Pandhu</h2>
                    <p class="text-xl font-medium text-gray-600 text-center">PM/Back-End Engineer</p>
                </div>
                <div class="flex flex-col items-center">
                    <img class="transition-transform transform hover:-translate-y-2 hover:rotate-2 mb-1" src="assets/img/zy.png" alt="Developer Image" class="w-11/12 mb-4">
                    <h2 class="text-3xl font-bold">Alif Azhar</h2>
                    <p class="text-xl font-medium text-gray-600 text-center">PM/Front-End Engineer</p>
                </div>
                <div class="flex flex-col items-center">
                    <img class="transition-transform transform hover:-translate-y-2 hover:rotate-2 mb-1" src="assets/img/kiw.png" alt="Developer Image" class=" w-11/12 mb-4">
                    <h2 class="text-3xl font-bold">Rizkwi Widianto</h2>
                    <p class="text-xl font-medium text-gray-600 text-center">ML Engineer</p>
                </div>
                <div class="flex flex-col items-center">
                    <img class="transition-transform transform hover:-translate-y-2 hover:rotate-6 mb-1" src="assets/img/dev.png" alt="Developer Image" class=" w-11/12 mb-4">
                    <h2 class="text-3xl font-bold">Devi Rizki</h2>
                    <p class="text-xl font-medium text-gray-600 text-center">ML Engineer</p>
                </div>
            </div>
        </div>
    </section>

    <div>
        <!-- Replace 'wave.svg' with your actual SVG file path -->
        <img src="/assets/img/wave.svg" alt="Wave SVG">
    </div>

    <script src="js/app.js"></script>
    <script src="https://unpkg.com/typed.js@2.1.0/dist/typed.umd.js"></script>

        <!-- Setup and start animation! -->
    <script>
        var typed = new Typed(".auto-type1", {
        strings: ['Easier.', 'Easier.','Easier.'],
        typeSpeed: 150,
        backSpeed: 150,
        loop: true
        });
    </script>
     <!-- Setup and start animation! -->
     <script>
        var typed = new Typed(".auto-type3", {
        strings: ['Effortlessly.', 'Effortlessly.','Effortlessly.'],
        typeSpeed: 150,
        backSpeed: 150,
        loop: true
        });
    </script>
            <!-- Setup and start animation! -->
    <script>
        var typed = new Typed(".auto-type2", {
        strings: ['Smarter.', 'Smarter.','Smarter.'],
        typeSpeed: 300,
        backSpeed: 300,
        loop: true
        });
    </script>

        <!-- Setup and start animation! -->
    <script>
        var typed = new Typed(".auto-type4", {
        strings: ['Works?', 'Run?','Operates?'],
        typeSpeed: 100,
        backSpeed: 200,
        loop: true
        });
    </script>

            <!-- Setup and start animation! -->
    <script>
        var typed = new Typed(".auto-type5", {
        strings: ['Developers.', 'Coders.','Founders.'],
        typeSpeed: 90,
        backSpeed: 170,
        loop: true
        });
    </script>
 <script>
      window.addEventListener('scroll', function() {
          var navbar = document.getElementById('navbar');
          console.log('Scroll Y:', window.scrollY); // Debugging line to check scroll position
          if (window.scrollY > 50) {
              console.log('Adding glassy class'); // Debugging line to confirm class addition
              navbar.classList.add('navbar-glassy');
          } else {
              console.log('Removing glassy class'); // Debugging line to confirm class removal
              navbar.classList.remove('navbar-glassy');
          }
      });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('menu-btn').addEventListener('click', function() {
                var menu = document.getElementById('menu');
                menu.classList.toggle('hidden');
            });
        });
    </script>
</body>
</html>
