<template>
  <div class="card card-carousel overflow-hidden h-100 p-0">
    <div id="carouselExampleCaptions" class="carousel slide h-100" data-bs-ride="carousel">
      <div class="carousel-inner border-radius-lg h-100">
        <div
          class="carousel-item h-100 active"
          :style="{backgroundImage: `url(${carousel1})`,
      backgroundSize: 'cover'}"
        >
          <div class="carousel-caption d-none d-md-block bottom-0 text-start start-0 ms-5">
            <div class="icon icon-shape icon-sm bg-white text-center border-radius-md mb-3">
              <i class="ni ni-camera-compact text-dark opacity-10"></i>
            </div>
            <h5 class="text-white mb-1">Get started with Argon</h5>
            <p>There’s nothing I really wanted to do in life that I wasn’t able to get good at.</p>
          </div>
        </div>
        <div
          class="carousel-item h-100"
          :style="{backgroundImage: `url(${carousel2})`,
      backgroundSize: 'cover'}"
        >
          <div class="carousel-caption d-none d-md-block bottom-0 text-start start-0 ms-5">
            <div class="icon icon-shape icon-sm bg-white text-center border-radius-md mb-3">
              <i class="ni ni-bulb-61 text-dark opacity-10"></i>
            </div>
            <h5 class="text-white mb-1">Faster way to create web pages</h5>
            <p>That’s my skill. I’m not really specifically talented at anything except for the ability to learn.</p>
          </div>
        </div>
        <div
          class="carousel-item h-100"
          :style="{backgroundImage: `url(${carousel3})`,
      backgroundSize: 'cover'}"
        >
          <div class="carousel-caption d-none d-md-block bottom-0 text-start start-0 ms-5">
            <div class="icon icon-shape icon-sm bg-white text-center border-radius-md mb-3">
              <i class="ni ni-trophy text-dark opacity-10"></i>
            </div>
            <h5 class="text-white mb-1">Share with us your design tips!</h5>
            <p>Don’t be afraid to be wrong because you can’t learn anything from a compliment.</p>
          </div>
        </div>
      </div>
      <button
        class="carousel-control-prev w-5 me-3"
        type="button"
        data-bs-target="#carouselExampleCaptions"
        data-bs-slide="prev"
      >
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
      </button>
      <button
        class="carousel-control-next w-5 me-3"
        type="button"
        data-bs-target="#carouselExampleCaptions"
        data-bs-slide="next"
      >
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
      </button>
    </div>
  </div>
</template>

<script>
import carousel1 from '@/assets/img/carousel-1.jpg';
import carousel2 from '@/assets/img/carousel-2.jpg';
import carousel3 from '@/assets/img/carousel-3.jpg';

export default {
  name: "carousel",
  data: () => ({
    carousel1,
    carousel2,
    carousel3,
  }),
};
</script>
