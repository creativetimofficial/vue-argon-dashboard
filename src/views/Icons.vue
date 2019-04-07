<template>
    <div>
        <base-header type="gradient-success" class="pb-6 pb-8 pt-5 pt-md-8">
            <!-- Card stats -->
            <div class="row">
                <div class="col-xl-3 col-lg-6">
                    <stats-card title="Total traffic"
                                type="gradient-red"
                                sub-title="350,897"
                                icon="ni ni-active-40"
                                class="mb-4 mb-xl-0"
                    >

                        <template slot="footer">
                            <span class="text-success mr-2"><i class="fa fa-arrow-up"></i> 3.48%</span>
                            <span class="text-nowrap">Since last month</span>
                        </template>
                    </stats-card>
                </div>
                <div class="col-xl-3 col-lg-6">
                    <stats-card title="Total traffic"
                                type="gradient-orange"
                                sub-title="2,356"
                                icon="ni ni-chart-pie-35"
                                class="mb-4 mb-xl-0"
                    >

                        <template slot="footer">
                            <span class="text-success mr-2"><i class="fa fa-arrow-up"></i> 12.18%</span>
                            <span class="text-nowrap">Since last month</span>
                        </template>
                    </stats-card>
                </div>
                <div class="col-xl-3 col-lg-6">
                    <stats-card title="Sales"
                                type="gradient-green"
                                sub-title="924"
                                icon="ni ni-money-coins"
                                class="mb-4 mb-xl-0"
                    >

                        <template slot="footer">
                            <span class="text-danger mr-2"><i class="fa fa-arrow-down"></i> 5.72%</span>
                            <span class="text-nowrap">Since last month</span>
                        </template>
                    </stats-card>

                </div>
                <div class="col-xl-3 col-lg-6">
                    <stats-card title="Performance"
                                type="gradient-info"
                                sub-title="49,65%"
                                icon="ni ni-chart-bar-32"
                                class="mb-4 mb-xl-0"
                    >

                        <template slot="footer">
                            <span class="text-success mr-2"><i class="fa fa-arrow-up"></i> 54.8%</span>
                            <span class="text-nowrap">Since last month</span>
                        </template>
                    </stats-card>
                </div>
            </div>
        </base-header>

        <div class="container-fluid mt--7">
            <div class="row">
                <div class="col">
                    <div class="card shadow">
                        <div class="card-header bg-transparent">
                            <h3 class="mb-0">Icons</h3>
                        </div>
                        <div class="card-body">
                            <div class="row icon-examples">
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard" data-clipboard-text="active-40"
                                            title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-active-40"></i>
                                            <span>active-40</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard" data-clipboard-text="air-baloon"
                                            title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-air-baloon"></i>
                                            <span>air-baloon</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard" data-clipboard-text="album-2"
                                            title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-album-2"></i>
                                            <span>album-2</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard" data-clipboard-text="align-center"
                                            title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-align-center"></i>
                                            <span>align-center</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard" data-clipboard-text="align-left-2"
                                            title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-align-left-2"></i>
                                            <span>align-left-2</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard" data-clipboard-text="ambulance"
                                            title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-ambulance"></i>
                                            <span>ambulance</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard" data-clipboard-text="app"
                                            title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-app"></i>
                                            <span>app</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard" data-clipboard-text="archive-2"
                                            title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-archive-2"></i>
                                            <span>archive-2</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard" data-clipboard-text="atom"
                                            title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-atom"></i>
                                            <span>atom</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard" data-clipboard-text="badge"
                                            title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-badge"></i>
                                            <span>badge</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard" data-clipboard-text="bag-17"
                                            title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-bag-17"></i>
                                            <span>bag-17</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard" data-clipboard-text="basket"
                                            title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-basket"></i>
                                            <span>basket</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard" data-clipboard-text="bell-55"
                                            title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-bell-55"></i>
                                            <span>bell-55</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard" data-clipboard-text="bold-down"
                                            title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-bold-down"></i>
                                            <span>bold-down</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard" data-clipboard-text="bold-left"
                                            title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-bold-left"></i>
                                            <span>bold-left</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard" data-clipboard-text="bold-right"
                                            title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-bold-right"></i>
                                            <span>bold-right</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard" data-clipboard-text="bold-up"
                                            title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-bold-up"></i>
                                            <span>bold-up</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard" data-clipboard-text="bold"
                                            title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-bold"></i>
                                            <span>bold</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard" data-clipboard-text="book-bookmark"
                                            title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-book-bookmark"></i>
                                            <span>book-bookmark</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard" data-clipboard-text="books"
                                            title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-books"></i>
                                            <span>books</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard" data-clipboard-text="box-2"
                                            title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-box-2"></i>
                                            <span>box-2</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard" data-clipboard-text="briefcase-24"
                                            title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-briefcase-24"></i>
                                            <span>briefcase-24</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard" data-clipboard-text="building"
                                            title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-building"></i>
                                            <span>building</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard" data-clipboard-text="bulb-61"
                                            title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-bulb-61"></i>
                                            <span>bulb-61</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard"
                                            data-clipboard-text="bullet-list-67" title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-bullet-list-67"></i>
                                            <span>bullet-list-67</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard" data-clipboard-text="bus-front-12"
                                            title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-bus-front-12"></i>
                                            <span>bus-front-12</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard" data-clipboard-text="button-pause"
                                            title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-button-pause"></i>
                                            <span>button-pause</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard" data-clipboard-text="button-play"
                                            title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-button-play"></i>
                                            <span>button-play</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard" data-clipboard-text="button-power"
                                            title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-button-power"></i>
                                            <span>button-power</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard"
                                            data-clipboard-text="calendar-grid-58" title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-calendar-grid-58"></i>
                                            <span>calendar-grid-58</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard"
                                            data-clipboard-text="camera-compact" title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-camera-compact"></i>
                                            <span>camera-compact</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard" data-clipboard-text="caps-small"
                                            title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-caps-small"></i>
                                            <span>caps-small</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard" data-clipboard-text="cart"
                                            title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-cart"></i>
                                            <span>cart</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard" data-clipboard-text="chart-bar-32"
                                            title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-chart-bar-32"></i>
                                            <span>chart-bar-32</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard" data-clipboard-text="chart-pie-35"
                                            title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-chart-pie-35"></i>
                                            <span>chart-pie-35</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard" data-clipboard-text="chat-round"
                                            title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-chat-round"></i>
                                            <span>chat-round</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard" data-clipboard-text="check-bold"
                                            title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-check-bold"></i>
                                            <span>check-bold</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard" data-clipboard-text="circle-08"
                                            title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-circle-08"></i>
                                            <span>circle-08</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard"
                                            data-clipboard-text="cloud-download-95" title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-cloud-download-95"></i>
                                            <span>cloud-download-95</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard"
                                            data-clipboard-text="cloud-upload-96" title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-cloud-upload-96"></i>
                                            <span>cloud-upload-96</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard" data-clipboard-text="compass-04"
                                            title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-compass-04"></i>
                                            <span>compass-04</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard" data-clipboard-text="controller"
                                            title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-controller"></i>
                                            <span>controller</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard" data-clipboard-text="credit-card"
                                            title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-credit-card"></i>
                                            <span>credit-card</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard" data-clipboard-text="curved-next"
                                            title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-curved-next"></i>
                                            <span>curved-next</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard" data-clipboard-text="delivery-fast"
                                            title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-delivery-fast"></i>
                                            <span>delivery-fast</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard" data-clipboard-text="diamond"
                                            title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-diamond"></i>
                                            <span>diamond</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard" data-clipboard-text="email-83"
                                            title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-email-83"></i>
                                            <span>email-83</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard" data-clipboard-text="fat-add"
                                            title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-fat-add"></i>
                                            <span>fat-add</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard" data-clipboard-text="fat-delete"
                                            title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-fat-delete"></i>
                                            <span>fat-delete</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard" data-clipboard-text="fat-remove"
                                            title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-fat-remove"></i>
                                            <span>fat-remove</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard" data-clipboard-text="favourite-28"
                                            title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-favourite-28"></i>
                                            <span>favourite-28</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard" data-clipboard-text="folder-17"
                                            title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-folder-17"></i>
                                            <span>folder-17</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard" data-clipboard-text="glasses-2"
                                            title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-glasses-2"></i>
                                            <span>glasses-2</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard" data-clipboard-text="hat-3"
                                            title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-hat-3"></i>
                                            <span>hat-3</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard" data-clipboard-text="headphones"
                                            title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-headphones"></i>
                                            <span>headphones</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard" data-clipboard-text="html5"
                                            title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-html5"></i>
                                            <span>html5</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard" data-clipboard-text="istanbul"
                                            title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-istanbul"></i>
                                            <span>istanbul</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard" data-clipboard-text="circle-08"
                                            title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-circle-08"></i>
                                            <span>circle-08</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard" data-clipboard-text="key-25"
                                            title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-key-25"></i>
                                            <span>key-25</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard" data-clipboard-text="laptop"
                                            title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-laptop"></i>
                                            <span>laptop</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard" data-clipboard-text="like-2"
                                            title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-like-2"></i>
                                            <span>like-2</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard"
                                            data-clipboard-text="lock-circle-open" title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-lock-circle-open"></i>
                                            <span>lock-circle-open</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard" data-clipboard-text="map-big"
                                            title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-map-big"></i>
                                            <span>map-big</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard" data-clipboard-text="mobile-button"
                                            title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-mobile-button"></i>
                                            <span>mobile-button</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard" data-clipboard-text="money-coins"
                                            title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-money-coins"></i>
                                            <span>money-coins</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard" data-clipboard-text="note-03"
                                            title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-note-03"></i>
                                            <span>note-03</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard"
                                            data-clipboard-text="notification-70" title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-notification-70"></i>
                                            <span>notification-70</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard" data-clipboard-text="palette"
                                            title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-palette"></i>
                                            <span>palette</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard" data-clipboard-text="paper-diploma"
                                            title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-paper-diploma"></i>
                                            <span>paper-diploma</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard" data-clipboard-text="pin-3"
                                            title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-pin-3"></i>
                                            <span>pin-3</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard" data-clipboard-text="planet"
                                            title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-planet"></i>
                                            <span>planet</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard" data-clipboard-text="ruler-pencil"
                                            title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-ruler-pencil"></i>
                                            <span>ruler-pencil</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard" data-clipboard-text="satisfied"
                                            title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-satisfied"></i>
                                            <span>satisfied</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard" data-clipboard-text="scissors"
                                            title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-scissors"></i>
                                            <span>scissors</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard" data-clipboard-text="send"
                                            title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-send"></i>
                                            <span>send</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard"
                                            data-clipboard-text="settings-gear-65" title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-settings-gear-65"></i>
                                            <span>settings-gear-65</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard" data-clipboard-text="settings"
                                            title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-settings"></i>
                                            <span>settings</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard" data-clipboard-text="single-02"
                                            title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-single-02"></i>
                                            <span>single-02</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard"
                                            data-clipboard-text="single-copy-04" title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-single-copy-04"></i>
                                            <span>single-copy-04</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard" data-clipboard-text="sound-wave"
                                            title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-sound-wave"></i>
                                            <span>sound-wave</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard" data-clipboard-text="spaceship"
                                            title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-spaceship"></i>
                                            <span>spaceship</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard" data-clipboard-text="square-pin"
                                            title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-square-pin"></i>
                                            <span>square-pin</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard" data-clipboard-text="support-16"
                                            title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-support-16"></i>
                                            <span>support-16</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard" data-clipboard-text="tablet-button"
                                            title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-tablet-button"></i>
                                            <span>tablet-button</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard" data-clipboard-text="tag"
                                            title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-tag"></i>
                                            <span>tag</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard" data-clipboard-text="tie-bow"
                                            title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-tie-bow"></i>
                                            <span>tie-bow</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard" data-clipboard-text="time-alarm"
                                            title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-time-alarm"></i>
                                            <span>time-alarm</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard" data-clipboard-text="trophy"
                                            title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-trophy"></i>
                                            <span>trophy</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard" data-clipboard-text="tv-2"
                                            title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-tv-2"></i>
                                            <span>tv-2</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard" data-clipboard-text="umbrella-13"
                                            title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-umbrella-13"></i>
                                            <span>umbrella-13</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard" data-clipboard-text="user-run"
                                            title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-user-run"></i>
                                            <span>user-run</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard" data-clipboard-text="vector"
                                            title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-vector"></i>
                                            <span>vector</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard" data-clipboard-text="watch-time"
                                            title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-watch-time"></i>
                                            <span>watch-time</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard" data-clipboard-text="world"
                                            title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-world"></i>
                                            <span>world</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard" data-clipboard-text="zoom-split-in"
                                            title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-zoom-split-in"></i>
                                            <span>zoom-split-in</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard" data-clipboard-text="collection"
                                            title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-collection"></i>
                                            <span>collection</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard" data-clipboard-text="image"
                                            title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-image"></i>
                                            <span>image</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard" data-clipboard-text="shop"
                                            title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-shop"></i>
                                            <span>shop</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard" data-clipboard-text="ungroup"
                                            title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-ungroup"></i>
                                            <span>ungroup</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard" data-clipboard-text="world-2"
                                            title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-world-2"></i>
                                            <span>world-2</span>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="button" class="btn-icon-clipboard" data-clipboard-text="ui-04"
                                            title="Copy to clipboard">
                                        <div>
                                            <i class="ni ni-ui-04"></i>
                                            <span>ui-04</span>
                                        </div>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>
<script>
  export default {};
</script>
<style></style>
