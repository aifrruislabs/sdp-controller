<template>
    <div>
        <nav class="navbar navbar-custom">
            <div class="container-fluid">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar" style="background-color: white;"></span>
                    <span class="icon-bar" style="background-color: white;"></span>
                    <span class="icon-bar" style="background-color: white;"></span>
                </button>
                <router-link class="navbar-brand" to="/">Smart SDP</router-link>
                </div>

                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    <li class="active" v-if="isAuthenticated">
                        <router-link to="/dashboard"><i class="fa fa-home" aria-hidden="true"></i>
                            &nbsp;Dashboard</router-link>
                    </li>

                    <li v-else>
                        <router-link to="/"><i class="fa fa-home" aria-hidden="true"></i>
                            &nbsp;Home</router-link>
                    </li>

                    <li class="active" v-if="isAuthenticated && getUserLevel == 1">
                        <router-link to="/admin/services"><i class="fa fa-television" aria-hidden="true"></i>
                            &nbsp;Services</router-link>
                    </li>

                    <li class="dropdown" v-if="isAuthenticated && getUserLevel == 2">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-cog" aria-hidden="true"></i>&nbsp;SDP Configuration <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><router-link to="/sdp/gateway" style="color: black;">SDP Gateways</router-link></li>
                            <li role="separator" class="divider"></li>

                            <li><router-link to="/sdp/clients" style="color: black;">SDP Clients</router-link></li>
                            <li role="separator" class="divider"></li>

                            <li><router-link to="/trust/score/weights" style="color: black;">Trust Score Weights</router-link></li>
                            <li role="separator" class="divider"></li>

                            <li><router-link to="/trust/score/policies" style="color: black;">Trust Score Policies</router-link></li>
                            <li role="separator" class="divider"></li>

                            <li><router-link to="/incident/response/policies" style="color: black;">Incident Response Policies</router-link></li>
                            <li role="separator" class="divider"></li>
                        </ul>
                    </li>

                    <li class="dropdown" v-if="isAuthenticated && getUserLevel == 2">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-cog" aria-hidden="true"></i>&nbsp;Score Metrics <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><router-link to="/face/recognition/manage/faces" style="color: black;">Face Recognition</router-link></li>
                            <li role="separator" class="divider"></li>

                            <li><router-link to="/manage/clients/mac/address" style="color: black;">Mac Addresses</router-link></li>
                            <li role="separator" class="divider"></li>

                            <li><router-link to="/manage/clients/geo/location" style="color: black;">Geo Location</router-link></li>
                            <li role="separator" class="divider"></li>

                        </ul>
                    </li>

                    <!-- <li class="active" v-if="isAuthenticated">
                        <router-link to="/trust/scores"><i class="fa fa-balance-scale" aria-hidden="true"></i>
                            &nbsp;Trust Scores</router-link>
                    </li> -->

                    <li class="active" v-if="!isAuthenticated">
                        <router-link to="/features"><i class="fa fa-snowflake-o" aria-hidden="true"></i>
                            &nbsp;Features</router-link>
                    </li>

                    <li class="active" v-if="!isAuthenticated">
                        <router-link to="/tutorials"><i class="fa fa-bookmark-o" aria-hidden="true"></i>
                            &nbsp;Tutorials</router-link>
                    </li>

                    <li class="active" v-if="!isAuthenticated">
                        <router-link to="/demo/videos"><i class="fa fa-television" aria-hidden="true"></i>
                            &nbsp;Demo Videos</router-link>
                    </li>

                    <li class="active" v-if="!isAuthenticated">
                        <router-link to="/pricing"><i class="fa fa-usd" aria-hidden="true"></i>
                            &nbsp;Pricing</router-link>
                    </li>
                    
                    <li v-if="!isAuthenticated">
                        <router-link to="/about"><i class="fa fa-spinner" aria-hidden="true"></i>
                            &nbsp;About</router-link>
                    </li>
                </ul>

                <ul class="nav navbar-nav navbar-right" v-if="!isAuthenticated">
                    <li>
                        <router-link to="/auth/login"><i class="fa fa-user-o" aria-hidden="true"></i>
                        &nbsp;Login</router-link>
                    </li>

                    <li>
                        <router-link to="/auth/create/new/account"><i class="fa fa-user-plus" aria-hidden="true"></i>
                        &nbsp;Register</router-link>
                    </li>
                </ul>

                <ul class="nav navbar-nav navbar-right" v-else>
                    <li>
                        <a href="#"><i class="fa fa-user-o" aria-hidden="true"></i>&nbsp;
                            {{ welcomeText }}
                        </a>
                    </li>

                    <li>
                        <a href="#" @click="logout"><i class="fa fa-share" aria-hidden="true"></i>&nbsp;Logout</a>
                    </li>
                </ul>

                </div><!-- /.navbar-collapse -->
            </div><!-- /.container-fluid -->
            </nav>
    </div>
</template>


<script>

import axios from 'axios'
import Swal from 'sweetalert2'
import { mapGetters } from 'vuex'
import router from './../../router/index'

    export default {
        name: 'HeaderComponent',
        
        data() {
            return {
                welcomeText: 'Welcome ' + this.$store.state.user.firstName
            }
        },

        methods: {

            async logout() {
                await axios.post(this.$store.state.baseApi + "/api/v1/auth/logout", {
                    email: this.email
                }, {
                    headers: {
                        'userId': this.$store.getters.getAuthId,
                        'authToken': this.$store.getters.getAuthToken
                    }
                }).then( (response) => {

                    const resData = response.data

                    const data = resData.data;

                    if (data['status'] == true) {
                        //Logged Out Success
                        this.$store.commit('authLogout')

                        //Redirect to Index Page
                        try{
                            router.push('/')
                        }catch (error) {
                            console.log("Error Occured : " + error.message)
                        }
                        

                    }else {
                        //Failed to Logout - Toast
                        Swal.fire({
                            title: 'Error!',
                            text: 'Failed to Logout. Please Try Again Later',
                            icon: 'error',
                            confirmButtonText: 'Ok'
                        })
                    }

                } ).catch( (error) => {

                    console.log('Error Occured : ' + error.message)

                    //Logged Out Success
                    this.$store.commit('authLogout')

                })
            }

        },

    
        computed: {
            ...mapGetters(
                ['isAuthenticated', 'getUserLevel']
            )
        },


    }

</script>


<style scoped>
    .black-li {
        color: black;
    }
</style>