<template>
    <div>

        <div class="row">
            <div class="col-md-8">
                <h4>Manage Clients Mac Address</h4>
            </div>

            <div class="col-md-4" >
                <div class="right">
                    <i class="fa fa-plus" style="font-size: 20px;" 
                        aria-hidden="true" data-toggle="modal" data-target="#addNewClientMacModal"></i>
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
                    <td>Client ID</td>
                    <td>Mac Address</td>
                    <td>Actions</td>
                </tr>

                <tr v-for="(clientMac, id) in clientsMacList" :key="clientMac.id">
                    <td>{{ id + 1}}</td>
                    <td>{{ clientMac.firstName }}</td>
                    <td>{{ clientMac.lastName }}</td>
                    <td>{{ clientMac.clientId }}</td>
                    <td>{{ clientMac.macAddr }}</td>
                    <td>
                        <div style="width: 60%;">
                                <div class="float-left">
                                    <i class="fa fa-pencil" 
                                     style="font-size: 24px; color: green;" aria-hidden="true"></i>
                                </div>

                                <div class="float-right">
                                    <i class="fa fa-times"
                                     @click="deleteMacAddress(clientMac.id)" 
                                     style="font-size: 24px; color: red;" aria-hidden="true"></i>
                                </div>
                            </div>
                    </td>
                </tr>
            </table>

        </div>

        <!-- Add New Client Modal -->
        <div class="modal fade" id="addNewClientMacModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Add New Mac to Client</h4>
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
                        <td>Mac Address</td>
                        <td><input type="text" v-model="macAddr" placeholder="Enter Mac Address aa:bb:cc:dd:ee:ff" class="form-control"></td>
                    </tr>

                    <tr>
                        <td></td>
                        <td><button class="btn btn-primary form-control" @click="addMacFirClient()">Add Mac for Client</button></td>
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
    name: 'ManageClientMacAddressComponent',

    data() {
      
        return {
            
            macAddr: '',

            clientId: '',

            clients: [],

            clientsMacList: [],

        }

    },

    methods: {

        async deleteMacAddress(clientMacId) {

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
                        
                        axios.post(this.$store.state.baseApi + "/api/v1/user/delete/client/mac", 
                            {
                                'clientMacId' : clientMacId,
                            },

                            { 
                                headers : {
                                    'Content-Type': 'application/json',
                                    userId: this.$store.getters.getAuthId,
                                    authToken: this.$store.getters.getAuthToken
                                }

                            })
                            
                            .then( (response) => {

                                const resData = response.data

                                if (resData['status'] == true) {
                                    Swal.fire(
                                        'Success!',
                                        'Mac Address was Deleted Successfully',
                                        'success'
                                        ).then(function () {
                                            window.location.reload()
                                        })
                                }else {
                                    Swal.fire(
                                        'Error!',
                                        'Failed to Delete Mac Address. Please Try Again',
                                        'error'
                                        )
                                }

                            });
                        
                    }
                })

        },

        async addMacFirClient() {

            await axios.post(this.$store.state.baseApi + "/api/v1/user/post/client/mac", {

                        'macAddr': this.macAddr,
                        'clientId' : this.clientId

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
                                'Mac was Added Successfully to Client',
                                'success'
                                ).then(function () {
                                    window.location.reload()
                                })
                    }else {
                        Swal.fire(
                                'Error!',
                                'Failed to Add Mac Address to Client',
                                'error'
                                )
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

        async getClientsMacList()  {

            await axios.get(this.$store.state.baseApi + "/api/v1/user/get/all/clients/macs/list", { 
                
                    headers : {
                        'Content-Type': 'application/json',
                        userId: this.$store.getters.getAuthId,
                        authToken: this.$store.getters.getAuthToken
                    }

                })
                
                .then( (response) => {

                    const resData = response.data

                    this.clientsMacList = resData.data

                })

        }

    },

    
    created() {
     
        //Get Clients Mac Address
        this.getClientsMacList()

        //Get Clients List
        this.getClientsList()
    }
}

</script>

<style scoped>

</style>