<template>
    <div class="container-fluid">
        
        <div class="row">
            <div class="col-md-8">
                <h4>SDP Services Gateways</h4>
            </div>

            <div class="col-md-4" >
                <div class="right">
                    <i class="fa fa-plus" style="font-size: 20px;" 
                        aria-hidden="true" data-toggle="modal" @click="clearModalDatas" data-target="#addNewgatewayModal"></i>
                </div>
            </div>
        </div>
        <hr/>

        <div class="row">

            <div class="col-md-12">

                <table class="table table-bordered">
                    <tr>
                        <td>ID</td>
                        <td>Title</td>
                        <td>Info</td>
                        <td>Accessibility</td>
                        <td>IP Address</td>
                        <td>Config</td>
                        <td>Statistics</td>
                        <td>Services</td>
                        <td>Actions</td>
                    </tr>

                    <tr v-for="(gateway, id) in gatewaysList" :key="gateway.id">
                        <td>{{ id + 1 }}</td>
                        <td>{{ gateway.gatewayTitle }}</td>
                        <td>{{ gateway.gatewayInfo }}</td>
                        <td>{{ gateway.gatewayNetworkAccessibility == "0" ? "Internal" : "External" }}</td>
                        <td>{{ gateway.gatewayIP }}</td>

                        <td style="width: 150px;">
                            
                                {
                                "controller_uri": "http://sdpapi.aifrruislabs.com",

                                "gateway_id": "{{ gateway.id }}",

                                "gateway_user_id": "{{ gUserId }}",

                                "gateway_iface": "eth0",

                                "gateway_access_token": "{{ gateway.gatewayAccessToken }}" 
                    
                                }</td>

                        <td style="width: 150px;">
                            <router-link :to="{ path: '/snort/gateway/alerts', query: { gatewayId: gateway.id }}">
                                <button class="btn btn-danger form-control">
                                    <i class="fa fa-spinner" aria-hidden="true"></i>&nbsp;Snort Alerts</button>
                            </router-link>
                            <br/><br/>
                            <router-link :to="{ path: '/gateway/view/statistics', query: { gatewayId: gateway.id }}">
                                <button class="btn btn-primary form-control">
                                    <i class="fa fa-opencart" aria-hidden="true"></i>&nbsp;View Statistics</button>
                            </router-link>
                        </td>

                        <td style="width: 150px;">
                            <button class="btn btn-success"
                            data-toggle="modal" data-target="#populateGatewayServicesModal" 
                            @click="populateServicesonModal(gateway.id)">
                            <i class="fa fa-fort-awesome" aria-hidden="true"></i>&nbsp;Manage Services</button>
                        </td>
                        
                        <td>
                            <div style="width: 100%;">
                                <div class="float-left">
                                    <i class="fa fa-pencil" 
                                    data-toggle="modal" data-target="#editGatewayInfoModal"
                                    @click="editGatewayInfo(gateway.id, gateway.gatewayTitle, gateway.gatewayInfo, gateway.gatewayNetworkAccessibility,
                                    gateway.gatewayIP, gateway.gatewayAccessToken)" style="font-size: 24px; color: green;" aria-hidden="true"></i>
                                </div>

                                <div class="float-right">
                                    <i class="fa fa-times" 
                                    @click="deleteGatewayInfo(gateway.id)" style="font-size: 24px; color: red;" aria-hidden="true"></i>
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>

            </div>

        </div>
                

        <!-- Edit Gateway Info Modal -->
        <div class="modal fade" id="editGatewayInfoModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Edit Gateway</h4>
            </div>
            <div class="modal-body">
                <table>
                    <tr>
                        <td>Gateway Title</td>
                        <td><input type="text" v-model="gatewayTitle" class="form-control"></td>
                    </tr>

                    <tr>
                        <td>Gateway Info</td>
                        <td><input type="text" v-model="gatewayInfo" class="form-control"></td>
                    </tr>

                    <tr>
                        <td>Network Accessibility</td>
                        <td>
                            <select v-model="gatewayNetworkAccessibility" class="form-control">
                                <option value="0">Internal Network</option>
                                <option value="1">External Network</option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td>Gateway IP</td>
                        <td><input type="text" v-model="gatewayIP" class="form-control"></td>
                    </tr>

                    <tr>
                        <td>Gateway Token</td>
                        <td><input type="text" disabled="true" v-model="gatewayAccessToken" class="form-control"></td>
                    </tr>

                    <tr>
                        <td></td>
                        <td>
                            <button @click="updateGateway()" class="btn btn-primary form-control">Update Gateway</button>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer">
            </div>
            </div>
        </div>
        </div>


        <!-- Populate Gateway Services Modal -->
        <div class="modal fade" id="populateGatewayServicesModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Manage Gateway Services</h4>
            </div>
            <div class="modal-body">
                <h3>Gateway Services</h3>
                <hr/>

                <table class="table">
                    <tr>
                        <td>ID</td>
                        <td>Service Title</td>
                        <td>Service Info</td>
                        <td>Service Port</td>
                        <td>Service Status</td>
                        <td>Actions</td>
                    </tr>

                    <tr v-for="(service, id) in gatewayServices" :key="service.id">
                        <td>{{ id + 1 }}</td>
                        <td>{{ service.serviceTitle }}</td>
                        <td>{{ service.serviceInfo }}</td>
                        <td>{{ service.servicePort }}</td>
                        <td class="center">
                            <toggle-button :value="service.serviceStatus == '1' ? true : false " 
                            @change="toggleGatewayService(id, service)" color="#82C7EB" :sync="true" :labels="true"/>
                        </td>
                        <td>
                            <div style="width: 100%;">
                                <i class="fa fa-times" @click="deleteGatewayService(service.id)" style="font-size: 24px; color: red;" aria-hidden="true"></i>
                            </div>
                        </td>
                    </tr>
                </table>


                <h3>Other Services</h3>
                <table class="table">
                    <tr>
                        <td>ID</td>
                        <td>Service Title</td>
                        <td>Service Info</td>
                        <td>Service Port</td>
                        <td>Actions</td>
                    </tr>

                    <tr v-for="(service, id) in otherGatewayServices" :key="service.id">
                        <td>{{ id + 1}}</td>
                        <td>{{ service.serviceTitle }}</td>
                        <td>{{ service.serviceInfo }}</td>
                        <td>{{ service.servicePort }}</td>
                        <td>
                            <div style="width: 100%;">
                                <i class="fa fa-plus" @click="addThisServiceToGateway(service.id)" style="font-size: 24px; color: green;" aria-hidden="true"></i>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer">
            </div>
            </div>
        </div>
        </div>




        <!-- Add New Gateway Modal -->
        <div class="modal fade" id="addNewgatewayModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Add New Gateway</h4>
            </div>
            <div class="modal-body">
                
                <table>
                    <tr>
                        <td>Gateway Title</td>
                        <td><input type="text" class="form-control" placeholder="Gateway Title" 
                            v-model="gatewayTitle"></td>
                    </tr>

                    <tr>
                        <td>Gateway Info</td>
                        <td><input type="text" class="form-control" placeholder="Gateway Info"
                            v-model="gatewayInfo"></td>
                    </tr>

                    <tr>
                        <td>Gateway IP</td>
                        <td><input type="text" class="form-control" placeholder="Gateway IP"
                            v-model="gatewayIP"></td>
                    </tr>

                    <tr>
                        <td>Network Accessibility</td>
                        <td>
                            <select v-model="gatewayNetworkAccessibility" class="form-control">
                                <option value="0">Internal Network</option>
                                <option value="1">External Network</option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td></td>
                        <td><button class="btn btn-primary form-control" @click="addNewgateway()">Add New Gateway</button></td>
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
    name: 'SDPServicesgatewaysComponent',

    data() {
      
        return {
            gatewayServiceSelectedId: '',

            gatewayId: '',
            gatewayTitle: '',
            gatewayInfo: '',
            gatewayNetworkAccessibility: '1',
            gatewayIP: '',
            gatewayAccessToken: '',

            gUserId: this.$store.getters.getAuthId,

            gatewaysList: [],

            gatewayServices : [],
            otherGatewayServices : []

        }

    },

    methods: {

        //Clear Modal Datas
        clearModalDatas() {
            this.gatewayId = ''
            this.gatewayTitle = ''
            this.gatewayInfo = ''
            this.gatewayNetworkAccessibility = '1'
            this.gatewayIP = ''
            this.gatewayAccessToken = ''
        },

        //Toggle Gateway Service
        async toggleGatewayService(index, service) {

            await axios.post(this.$store.state.baseApi + "/api/v1/toggle/gateway/service/status", 
                {
                    'serviceId' : service.serviceId,
                    'gatewayId' : service.gatewayId,
                    'serviceStatus' : service.serviceStatus
                },

                { 
                    headers : {
                        'Content-Type': 'application/json',
                        userId: this.$store.getters.getAuthId,
                        authToken: this.$store.getters.getAuthToken
                    }

                })
                
                .then( (response) => {

                    if (response.status == 201) {

                        if (this.gatewayServices[index]['serviceStatus'] == "1") {
                            this.gatewayServices[index]['serviceStatus'] = "0";
                        }else {
                            this.gatewayServices[index]['serviceStatus'] = "1";
                        }
                        
                    }else {

                        Swal.fire(
                                'Error!',
                                'Failed to Toggle Service Status. Make Sure Gateway is Already Connected by Checking Gateway Statistics',
                                'error'
                                )
                    }

                });

        },

        //Delete Gateway Service
        async deleteGatewayService(serviceId) {

            await axios.post(this.$store.state.baseApi + "/api/v1/user/gateway/delete/service", 
                {
                    'serviceId' : serviceId,
                    'gatewayId' : this.gatewayServiceSelectedId
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

                        this.populateServicesonModal(this.gatewayServiceSelectedId)

                    }

                });
        },

        async addThisServiceToGateway(serviceId) {

            axios.post(this.$store.state.baseApi + "/api/v1/user/gateway/add/service", 
                {
                    'serviceId' : serviceId,
                    'gatewayId' : this.gatewayServiceSelectedId
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

                        this.populateServicesonModal(this.gatewayServiceSelectedId)

                    }

                });

        },

        updateGateway() {

            axios.patch(this.$store.state.baseApi + "/api/v1/user/gateway/update", 
                {
                    'gatewayId' : this.gatewayId,
                    'gatewayTitle' : this.gatewayTitle,
                    'gatewayInfo' : this.gatewayInfo,
                    'gatewayNetworkAccessibility' : this.gatewayNetworkAccessibility,
                    'gatewayIP' : this.gatewayIP
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
                            'Gateway was Updated Successfully',
                            'success'
                            ).then(function () {
                                window.location.reload()
                            })
                    }else {
                        Swal.fire(
                            'Error!',
                            'Gateway was not Updated Successfully',
                            'error'
                            )
                    }

                });

        },

        pullGatewaysList() {

            axios.get(this.$store.state.baseApi + "/api/v1/user/gateway/get/all", { 
                    headers : {
                        'Content-Type': 'application/json',
                        userId: this.$store.getters.getAuthId,
                        authToken: this.$store.getters.getAuthToken
                    }

                })
                
                .then( (response) => {

                    const resData = response.data

                    const data = resData.data

                    this.gatewaysList = data

                });

        },

        //Delete Gateway Info
        async deleteGatewayInfo(gatewayId) {
            
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
                        axios.delete(this.$store.state.baseApi + "/api/v1/user/gateway/delete/"+gatewayId,
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
                                    'Gateway was Deleted Successfully',
                                    'success'
                                    ).then(function () {
                                        window.location.reload()
                                    });
                            }else {
                                Swal.fire(
                                    'Error!',
                                    'Failed to delete Gateway. Please Try Again Later',
                                    'error'
                                    )
                            }

                        })
                    }
                })

        },

        //Edit Gateway Info
        async editGatewayInfo(gatewayId, gatewayTitle, gatewayInfo, gatewayNetworkAccessibility, gatewayIP, gatewayAccessToken) {
            this.gatewayId = gatewayId
            this.gatewayTitle = gatewayTitle
            this.gatewayInfo = gatewayInfo
            this.gatewayNetworkAccessibility = gatewayNetworkAccessibility
            this.gatewayIP = gatewayIP
            this.gatewayAccessToken = gatewayAccessToken
        },

        //Populate Services on Gateway Services Modal
        async populateServicesonModal(gatewayId) {

            this.gatewayServiceSelectedId = gatewayId

            //Get Gateway Services
            await axios.get(this.$store.state.baseApi + "/api/v1/user/get/gateway/service/"+gatewayId, { 
                
                    headers : {
                        'Content-Type': 'application/json',
                        userId: this.$store.getters.getAuthId,
                        authToken: this.$store.getters.getAuthToken
                    }

            })
            
            .then( (response) => {

                const resData = response.data

                this.gatewayServices = resData['gatewayServices']
                this.otherGatewayServices = resData['otherGatewayServices']

            })

        },

        //Add New Gateway
        async addNewgateway() {
            
            if (this.gatewayTitle != "" && this.gatewayIP != "") {
                
                await axios.post(this.$store.state.baseApi + "/api/v1/user/gateway/create", {
                        gatewayTitle: this.gatewayTitle,
                        gatewayInfo: this.gatewayInfo,
                        gatewayNetworkAccessibility : this.gatewayNetworkAccessibility,
                        gatewayIP: this.gatewayIP,
                    }, { 
                    
                        headers : {
                            'Content-Type': 'application/json',
                            userId: this.$store.getters.getAuthId,
                            authToken: this.$store.getters.getAuthToken
                        }

                })
                
                .then( (response) => {

                    const resData = response.data

                    //Adding Gateway to Table
                    this.gatewaysList.shift(resData.data)

                    if (resData['status'] == true) {

                        this.gatewayTitle = this.gatewayInfo = this.gatewayIP = ""

                        Swal.fire({
                                title: 'Success!',
                                text: 'Gateway was Added Successfully',
                                icon: 'success',
                            }).then(function () {
                                window.location.reload()
                            })
                    }else {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Failed to create New Gateway. Please Try Again Later',
                            icon: 'warning',
                        })        
                    }
 
                });

            }else {
                //Toast
                Swal.fire({
                        title: 'Error!',
                        text: 'Please Enter Gateway Title and Gateway IP',
                        icon: 'warning',
                    })
            }

        }

    },

    created() {

        this.pullGatewaysList()

    }
}

</script>
