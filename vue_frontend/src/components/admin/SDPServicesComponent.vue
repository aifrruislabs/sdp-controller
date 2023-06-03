<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8">
                <h4>SDP Services</h4>
            </div>

            <div class="col-md-4 right">
                <i class="fa fa-plus" style="font-size: 20px;" 
                    data-toggle="modal" data-target="#createNewServiceModal"></i>
            </div>
        </div>
        <hr/>
        
        <table class="table">
            <tr>
                <td>ID</td>
                <td>Service Title</td>
                <td>Service Info</td>
                <td>Service Protocol</td>
                <td>Service Port</td>
                <td style="width: 120px;">Service Score</td>
                <td>Actions</td>
            </tr>

            <tr v-for="(service, id) in services" :key="service.id">
                <td>{{ id + 1}}</td>
                <td>{{ service.serviceTitle }}</td>
                <td>{{ service.serviceInfo }}</td>
                <td>{{ getServiceProtoTitle(service.serviceProto) }}</td>
                <td>{{ service.servicePort }}</td>
                <td>{{ service.serviceScore }}%</td>
                <td>
                    <div style="width: 100%;">
                        <div class="float-left">
                            <i class="fa fa-pencil" @click="setEditData(service.id, 
                            service.serviceTitle, service.serviceInfo, service.servicePort, service.serviceScore, service.serviceProto)" data-toggle="modal" data-target="#editServiceModal" style="font-size: 24px; color: green;" aria-hidden="true"></i>
                        </div>

                        <div class="float-right">
                            <i class="fa fa-times" @click="deleteService(service.id)" style="font-size: 24px; color: red;" aria-hidden="true"></i>
                        </div>
                    </div>
                </td>
            </tr>
        </table>

        <!-- Create New Service Modal -->
        <div class="modal fade" id="createNewServiceModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Create New Service</h4>
            </div>
            <div class="modal-body">
                
                <table>
                    <tr>
                        <td>Service Title</td>
                        <td><input type="text" class="form-control" v-model="serviceTitle"></td>
                    </tr>

                    <tr>
                        <td>Service Info</td>
                        <td><input type="text" class="form-control" v-model="serviceInfo"></td>
                    </tr>

                    <tr>
                        <td>Service Protocol</td>
                        <td>
                            <select class="form-control" v-model="selectedIdserviceProtocol">
                                <option 
                                    v-bind:value="serviceProtocol.id"
                                    v-for="serviceProtocol in serviceProtocols"
                                    :key="serviceProtocol.id">
                                    {{ serviceProtocol.title }}
                                </option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td>Service Port</td>
                        <td><input type="number" class="form-control" v-model="servicePort"></td>
                    </tr>

                    <tr>
                        <td>Service Score</td>
                        <td><input type="number" class="form-control" v-model="serviceScore"></td>
                    </tr>

                    <tr>
                        <td></td>
                        <td>
                            <button class="btn btn-primary form-control" @click="createNewService()">Create New Service</button>
                        </td>
                    </tr>
                </table>
                
            </div>
            <div class="modal-footer">
                
            </div>
            </div>
        </div>
        </div>

        <!-- Edit Service Modal -->
        <div class="modal fade" id="editServiceModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Edit Service</h4>
            </div>
            <div class="modal-body">
                <table>
                    <tr>
                        <td>Service Title</td>
                        <td><input type="text" v-model="serviceTitle" class="form-control"></td>
                    </tr>

                    <tr>
                        <td>Service Info</td>
                        <td><input type="text" v-model="serviceInfo" class="form-control"></td>
                    </tr>

                    <tr>
                        <td>Service Port</td>
                        <td><input type="text" v-model="servicePort" class="form-control"></td>
                    </tr>

                    <tr>
                        <td>Service Protocol</td>
                        <td>
                            <select class="form-control" v-model="toEditProtoId">
                                <option 
                                    v-bind:value="serviceProtocol.id"
                                    v-for="serviceProtocol in serviceProtocols"
                                    :key="serviceProtocol.id">
                                    {{ serviceProtocol.title }}
                                </option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td>Service Score</td>
                        <td><input type="text" v-model="serviceScore" class="form-control"></td>
                    </tr>

                    <tr>
                        <td></td>
                        <td>
                            <button class="btn btn-primary form-control" @click="updateService()">Update Service</button>
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
    name: 'SDPServicesComponent',

    data() {
      
        return {

            serviceId: '',

            serviceTitle: '',

            serviceInfo: '',

            servicePort: '',

            serviceProto: 'tcp',

            serviceScore: '',

            toEditProtoId: 1,

            selectedIdserviceProtocol: '1',

            serviceProtocols: [
                {id: 1, 'title' : 'TCP Protocol', 'shortCode': 'tcp'},
                {id: 2, 'title' : 'UDP Protocol', 'shortCode': 'udp'},
            ],

            services: []

        }

    },

    methods: {

        getServiceProtoTitle(serviceProtoId) {
            return this.serviceProtocols[parseInt(serviceProtoId) - 1]['title'];
        },

        async updateService() {

            await axios.patch(this.$store.state.baseApi + "/api/v1/admin/service/update",
                {
                    serviceId: this.serviceId,
                    serviceTitle: this.serviceTitle,
                    serviceInfo: this.serviceInfo,
                    servicePort: this.servicePort,
                    serviceScore: this.serviceScore,
                    serviceProto: this.toEditProtoId
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
                            'Service was Updated Successfully',
                            'success'
                            ).then(function () {
                                window.location.reload()
                            });
                }else {
                    Swal.fire(
                            'Error!',
                            'Failed to Update Service. Please Try Again Later',
                            'error'
                            )
                }

            })

        },

        setEditData(serviceId, serviceTitle, serviceInfo, servicePort, serviceScore, serviceProto) {
            this.serviceId = serviceId
            this.serviceTitle = serviceTitle
            this.serviceInfo = serviceInfo
            this.servicePort = servicePort
            this.serviceScore = serviceScore 
            this.selectedIdserviceProtocol = parseInt(serviceProto) - 1
            this.toEditProtoId = parseInt(serviceProto)
            this.serviceProto = this.serviceProtocols[parseInt(serviceProto) - 1]['shortCode']
        },

        deleteService(serviceId) {
            
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
                        axios.delete(this.$store.state.baseApi + "/api/v1/admin/service/delete/"+serviceId,
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
                                    'Service was Deleted Successfully',
                                    'success'
                                    ).then(function () {
                                        window.location.reload()
                                    });
                            }else {
                                Swal.fire(
                                    'Error!',
                                    'Failed to delete Service. Please Try Again Later',
                                    'error'
                                    )
                            }

                        })
                    }
                })
        },

        async pullAllServices() {

            await axios.get(this.$store.state.baseApi + "/api/v1/admin/services/get/all",
                { 
                headers : {
                    'Content-Type': 'application/json',
                    userId: this.$store.getters.getAuthId,
                    authToken: this.$store.getters.getAuthToken
                }

            })
            
            .then( (response) => {

                const resData = response.data

                this.services = resData.data

            })

        },

        async createNewService() {

            if (this.serviceTitle != "" && this.servicePort != "") {

                await axios.post(this.$store.state.baseApi + "/api/v1/admin/create/service",
                    {
                        serviceTitle: this.serviceTitle,
                        serviceInfo: this.serviceInfo,
                        servicePort: this.servicePort,
                        serviceScore: this.serviceScore,
                        serviceProto: this.selectedIdserviceProtocol
                    }, { 
                    headers : {
                        'Content-Type': 'application/json',
                        userId: this.$store.getters.getAuthId,
                        authToken: this.$store.getters.getAuthToken
                    }

                })
                
                .then( (response) => {

                    const resData = response.data

                    //Adding Data to Services
                    this.services.splice(resData.data)

                    if (resData['status'] == true) {
                        
                        Swal.fire({
                            title: 'Success!',
                            text: 'Service was Added Successfully.',
                            icon: 'success',
                        }).then(function () {
                            window.location.reload()
                        })
                    }else {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Failed to Create New Service. Please Try Again Later',
                            icon: 'warning',
                        })        
                    }

                })

            }else {
                Swal.fire({
                    title: 'Error!',
                    text: 'Please Enter Service Title and Service Port',
                    icon: 'warning',
                })        
            }

        }

    },

    created() {
        this.pullAllServices()
    }

}

</script>
