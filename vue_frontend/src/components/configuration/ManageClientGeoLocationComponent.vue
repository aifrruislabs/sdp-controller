<template>
    <div>
        <div class="row">
            <div class="col-md-8">
                <h4>Manage Clients Geo Locations</h4>
            </div>

            <div class="col-md-4" >
                <div class="right">
                    <i class="fa fa-plus" style="font-size: 20px;" 
                        aria-hidden="true" data-toggle="modal" data-target="#addNewClientGeoLocModal"></i>
                </div>
            </div>
        </div>
        <hr/>

        <div>

            <table class="table table-bordered">
                <tr>
                    <td>ID</td>
                    <td>First Name</td>
                    <td>Last Name</td>
                    <td>Client Id</td>
                    <td>Latitude</td>
                    <td>Longitude</td>
                    <td>KM Radius</td>
                    <td>Actions</td>
                </tr>

                <tr v-for="(clientGeo, id) in clientGeoLocs" :key="clientGeo.id">
                    <td>{{ id + 1}}</td>
                    <td>{{ clientGeo.firstName }}</td>
                    <td>{{ clientGeo.lastName }}</td>
                    <td>{{ clientGeo.clientId }}</td>
                    <td>{{ clientGeo.latitude }}</td>
                    <td>{{ clientGeo.longitude }}</td>
                    <td>{{ clientGeo.kilometreRadius }} Km</td>
                    <td>
                        <div style="width: 100%;">
                                <div class="float-left">
                                    <i class="fa fa-pencil" 
                                     style="font-size: 24px; color: green;" aria-hidden="true"></i>
                                </div>

                                <div class="float-right">
                                    <i class="fa fa-times" 
                                     @click="deleteGeoLoc(clientGeo.id)"
                                     style="font-size: 24px; color: red;" aria-hidden="true"></i>
                                </div>
                            </div>
                    </td>
                </tr>
            </table>

        </div>


        <!-- Add New Client Geo Loc Modal -->
        <div class="modal fade" id="addNewClientGeoLocModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Add Geo Location for Client</h4>
            </div>
            <div class="modal-body">
                <table>
                    <tr>
                        <td>Clients List</td>
                        <td>
                            <select v-model="clientId" class="form-control">
                                <option v-for="client in clients" :key="client.id" v-bind:value="client.clientId">
                                    {{ client.clientId }} - {{ client.firstName }} {{ client.lastName }}
                                </option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td>Latitude</td>
                        <td><input type="text" v-model="latitude" class="form-control"></td>
                    </tr>

                    <tr>
                        <td>Longitude</td>
                        <td><input type="text" v-model="longitude" class="form-control"></td>
                    </tr>

                    <tr>
                        <td>Kilometre Radius</td>
                        <td><input type="text" v-model="kilometreRadius" class="form-control"></td>
                    </tr>

                    <tr>
                        <td></td>
                        <td>
                            <button class="btn btn-primary form-control" @click="addNewGeoForClient()">
                                Add Geo Location for Client
                            </button>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer">
                
            </div>
            </div>
        </div>
        </div>

    </div>
</template>

<script>

import axios from 'axios'
import Swal from 'sweetalert2'

export default {
    name: 'ManageClientGeoLocation',

    data() {
      
        return {
            
            latitude: '',

            longitude: '',

            clientId: '',

            kilometreRadius: '',

            clients: '',

            clientGeoLocs: [],

        }

    },

    methods: {

        async deleteGeoLoc(geoLocId) {

            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
                }).then((result) => {

                if (result.isConfirmed) {
                
                
                    axios.post(this.$store.state.baseApi + "/api/v1/user/delete/client/geo/loc", {

                        'geoLocId': geoLocId

                        }, { 
                        
                            headers : {
                                'Content-Type': 'application/json',
                                userId: this.$store.getters.getAuthId,
                                authToken: this.$store.getters.getAuthToken
                            }

                    })
                    
                    .then( (response) => {

                        const resData = response.data

                        const jsonData = JSON.parse(JSON.stringify(resData))

                        if (jsonData['status'] == true) {
                            Swal.fire(
                                'Success!',
                                'Geo Location was Deleted Successfully',
                                'success'
                                ).then(function () {
                                    window.location.reload()
                                })
                        }else {
                            Swal.fire(
                                'Error!',
                                'Failed to Delete Geo Location. Please Try Again',
                                'error'
                                )
                        }

                    })


                    
                }
            })
        

        },

        async getClientsList() {
            
            await axios.get(this.$store.state.baseApi + "/api/v1/user/sdp/get/all/clients", { 
                    
                        headers : {
                            'Content-Type': 'application/json',
                            userId: this.$store.getters.getAuthId,
                            authToken: this.$store.getters.getAuthToken
                        }

                })
                
                .then( (response) => {

                    const resData = response.data

                    this.clients = resData.data

                    //Set Client Value to the First Client in the List
                    this.clientId = this.clients['0']['clientId']

                })
        },

        async addNewGeoForClient() {

            await axios.post(this.$store.state.baseApi + "/api/v1/user/create/client/geo/location", {

                    'latitude': this.latitude,

                    'longitude': this.longitude,

                    'clientId': this.clientId,

                    'kilometreRadius': this.kilometreRadius

                    }, { 
                    
                        headers : {
                            'Content-Type': 'application/json',
                            userId: this.$store.getters.getAuthId,
                            authToken: this.$store.getters.getAuthToken
                        }

                })
                
                .then( (response) => {

                    const resData = response.data

                    const jsonData = JSON.parse(JSON.stringify(resData))

                    if (jsonData['status'] == true) {
                        Swal.fire(
                                'Success!',
                                'Geo Location was Added Successfully to Client.',
                                'success'
                                ).then(function () {
                                    window.location.reload()
                                })
                    }else {
                        Swal.fire(
                                'Error!',
                                'Failed to Add Geo Location to Client',
                                'error'
                                )
                    }

                })

        },

        async getclientGeoLocs() {

            await axios.get(this.$store.state.baseApi + "/api/v1/user/get/all/client/geo/locs", { 
                    
                        headers : {
                            'Content-Type': 'application/json',
                            userId: this.$store.getters.getAuthId,
                            authToken: this.$store.getters.getAuthToken
                        }

                })
                
                .then( (response) => {

                    const resData = response.data

                    this.clientGeoLocs = JSON.parse(JSON.stringify(resData.data))

                })

        }

    },

    created() {

        //Get Clients Geo Locs
        this.getclientGeoLocs()

        //Get Clients List
        this.getClientsList()

    }
}

</script>

<style scoped>

</style>