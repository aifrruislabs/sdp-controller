<template>
    <div class="container-fluid">
        
        <div class="row">
            <div class="col-md-8">
                <h4>SDP Clients</h4>
            </div>

            <div class="col-md-4" >
                <div class="right">
                    <i class="fa fa-plus" style="font-size: 20px;" 
                        aria-hidden="true" data-toggle="modal" data-target="#addNewClientModal"></i>
                </div>
            </div>
        </div>
        <hr/>

        <table class="table">
            <tr>
                <td>ID</td>
                <td>First Name</td>
                <td>Middle Name</td>
                <td>Last Name</td>
                <td>Client ID</td>
                <td>Public IP</td>
                <td>Trust Score</td>
                <td>Username</td>
                <td>Manage</td>
                <td>Actions</td>
            </tr>

            <tr v-for="(client, id) in clients" :key="client.id">
                <td>{{ id + 1}}</td>
                <td>{{ client.firstName }}</td>
                <td>{{ client.middleName }}</td>
                <td>{{ client.lastName }}</td>
                <td>{{ client.clientId }}</td>
                <td>{{ client.publicIp }}</td>
                <td>{{ client.totalTrustScore }} %</td>
                <td>{{ client.username }}</td>
                <td>
                    <button class="btn btn-primary form-control" @click="pullGatewayServicesList(client.id)"
                    data-toggle="modal" data-target="#serviceAccessModal">Service Access</button>
                    <br/><br/>

                    <button class="btn btn-success form-control">Incident Events</button>
                </td>

                <td>
                    <div style="width: 100%;">
                        <div class="float-left">
                            <i class="fa fa-pencil" @click="setEditData(client.id, client.firstName, client.middleName,
                            client.lastName, client.username)" style="font-size: 24px; color: green;" aria-hidden="true"></i>
                        </div>

                        <div class="float-right">
                            <i class="fa fa-times" @click="deleteClient(client.id)" style="font-size: 24px; color: red;" aria-hidden="true"></i>
                        </div>
                    </div>

                </td>
            </tr>
        </table>
        

        <!-- Add New Client Modal -->
        <div class="modal fade" id="addNewClientModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Add New Client</h4>
            </div>
            <div class="modal-body">
                <table>
                    <tr>
                        <td>First Name</td>
                        <td><input type="text" v-model="clientFirstName" class="form-control"></td>
                    </tr>

                    <tr>
                        <td>Middle Name</td>
                        <td><input type="text" v-model="clientMiddleName" class="form-control"></td>
                    </tr>

                    <tr>
                        <td>Last Name</td>
                        <td><input type="text" v-model="clientLastName" class="form-control"></td>
                    </tr>

                    <tr>
                        <td>Username</td>
                        <td><input type="text" v-model="clientUsername" class="form-control"></td>
                    </tr>

                    <tr>
                        <td>Password</td>
                        <td><input type="password" v-model="clientPassword" class="form-control"></td>
                    </tr>

                    <tr>
                        <td>Verify Password</td>
                        <td><input type="password" v-model="clientPasswordVerification" class="form-control"></td>
                    </tr>

                    <tr>
                        <td></td>
                        <td>
                            <button @click="addNewClient()" class="btn  btn-primary form-control">Add New Client</button>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer">
            </div>
            </div>
        </div>
        </div>


        <!-- Services Access Modal -->
        <div class="modal fade" id="serviceAccessModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Manage Client Services Access</h4>
            </div>
            <div class="modal-body">
                <h4>Client with Access</h4>
                <hr/>
                <table class="table">
                    <tr>
                        <td>ID</td>
                        <td>Gateway</td>
                        <td>Client ID</td>
                        <td>Service Title</td>
                        <td>Service Port</td>
                        <td>Service Score</td>
                        <td>Actions</td>
                    </tr>

                    <tr v-for="(gSAL, id) in gatewayServiceAccessList" :key="gSAL.id">
                        <td>{{ id + 1 }}</td>
                        <td>{{ gSAL.gatewayTitle }}</td>
                        <td>{{ gSAL.clientId }}</td>
                        <td>{{ gSAL.serviceTitle }}</td>
                        <td>{{ gSAL.servicePort }}</td>
                        <td>{{ gSAL.serviceScore }} %</td>
                        <td>
                            <i class="fa fa-times" @click="removeGatewayServiceAccessToClient(gSAL.csaId, gSAL.clientId, gSAL.gatewayId)"
                            style="font-size: 24px; color: red;" aria-hidden="true"></i>
                        </td>
                    </tr>
                </table>


                <h4>Client with No Access</h4>
                <hr/>
                <table class="table">
                    <tr>
                        <td>ID</td>
                        <td>Gateway</td>
                        <td>Client ID</td>
                        <td>Service Title</td>
                        <td>Service Port</td>
                        <td>Service Score</td>
                        <td>Actions</td>
                    </tr>

                    <tr v-for="(gSNAL, id) in gatewayServiceNoAccessList" :key="gSNAL.id">
                        <td>{{ id + 1 }}</td>
                        <td>{{ gSNAL.gatewayTitle }}</td>
                        <td>{{ gSNAL.clientId }}</td>
                        <td>{{ gSNAL.serviceTitle }}</td>
                        <td>{{ gSNAL.servicePort }}</td>
                        <td>{{ gSNAL.serviceScore }} %</td>
                        <td>
                            <i class="fa fa-plus" @click="addUGatewayAccessService(gSNAL.clientId, gSNAL.serviceId, gSNAL.gatewayId);"
                            style="font-size: 24px; color: green;" aria-hidden="true"></i>
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
    name: 'SDPClientsComponent',

    data() {
      
        return {
            
            clientServiceCid: '', 

            clientFirstName: '',

            clientMiddleName: '',

            clientLastName: '',

            clientUsername: '',

            clientPassword: '',

            clientPasswordVerification: '',

            clients: [],

            gatewayServiceAccessList: [],
            
            gatewayServiceNoAccessList: [],

        }

    },

    methods: {

        async addUGatewayAccessService(clientId, serviceId, gatewayId) {

            axios.post(this.$store.state.baseApi + "/api/v1/user/gateway/client/add/service", {

                            'clientId' : clientId,
                            'serviceId' : serviceId,
                            'gatewayId' : gatewayId

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
                                //Refresh Gateway Services List
                                this.pullGatewayServicesList(this.clientServiceCid);

                                //Refresh Clients List
                                this.getClients()
                            }else {
                                Swal.fire(
                                    'Error!',
                                    'Failed to Add Service to Client',
                                    'error'
                                    )
                            }

                        })
        },

        async pullGatewayServicesList(clientId) {

            this.clientServiceCid = clientId

            axios.get(this.$store.state.baseApi + "/api/v1/user/gateway/client/services/list/"+clientId,
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
                                
                                this.gatewayServiceAccessList = resData.gatewayServiceAccessList
                                this.gatewayServiceNoAccessList = resData.gatewayServiceNoAccessList

                            }

                        })


        },

        async removeGatewayServiceAccessToClient(csaId, clientId, gatewayId) {

            axios.post(this.$store.state.baseApi + "/api/v1/user/delete/gateway/client/service", {
                        
                        'csaId' : csaId,

                        'clientId' : clientId,

                        'gatewayId' : gatewayId

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
                                //Refresh Gateway Services List
                                this.pullGatewayServicesList(this.clientServiceCid)                                

                                //Refresh Clients List
                                this.getClients()
                            }else {
                                Swal.fire(
                                    'Error!',
                                    'Service was not Removed to Client',
                                    'error'
                                    )
                            }

                        })

        },

        async deleteClient(clientId) {

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
                        axios.delete(this.$store.state.baseApi + "/api/v1/user/client/delete/"+clientId,
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
                                    'Client was Deleted Successfully',
                                    'success'
                                    ).then(function () {
                                        window.location.reload()
                                    });
                            }else {
                                Swal.fire(
                                    'Error!',
                                    'Failed to delete Client. Please Try Again Later',
                                    'error'
                                    )
                            }

                        })
                    }
                })

        },

        async getClients() {

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

            })

        },

        async addNewClient() {

            if (this.clientUsername != "" && this.clientPassword != "") {

                if (this.clientPassword == this.clientPasswordVerification) {
                        
                    await axios.post(this.$store.state.baseApi + "/api/v1/user/sdp/create/client", {
                            clientFirstName: this.clientFirstName,
                            clientMiddleName: this.clientMiddleName,
                            clientLastName: this.clientLastName,
                            clientUsername: this.clientUsername,
                            clientPassword: this.clientPassword
                        }, { 
                        
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
                                'New Client was Created Successfully',
                                'success'
                                ).then(function () {
                                    window.location.reload()
                                })
                        }else {
                            Swal.fire(
                                'Error!',
                                'Failed to Create New Client. Please Try Again Later',
                                'error'
                                )
                        }

                    })


                }else {
                    Swal.fire(
                            'Error!',
                            'Please Enter Correct Password Verification',
                            'error'
                            )
                }

            }else {
                Swal.fire(
                    'Error!',
                    'Please Enter Client Username and Password',
                    'error'
                    )
            }

        }

    },

    created() {
        this.getClients()
    }
}

</script>
