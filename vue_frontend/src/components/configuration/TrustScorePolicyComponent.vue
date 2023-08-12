<template>
    <div>
        <div class="row">
            <div class="col-md-8">
                <h4>Trust Score Policies</h4>
            </div>

            <div class="col-md-4" >
                <div class="right">
                    <i class="fa fa-plus" style="font-size: 20px;" 
                        aria-hidden="true" data-toggle="modal" data-target="#addNewTrustScorePolicyModal"></i>
                </div>
            </div>
        </div>
        <hr/>


        <div>
            <table class="table">
                <tr>
                    <td>ID</td>
                    <td>Service</td>
                    <td>Factor</td>
                    <td>Percent</td>
                    <td>Actions</td>
                </tr>

                <tr v-for="(trustScorePolicy, id) in trustScorePolicies" :key="trustScorePolicy.id">
                    <td>{{ id + 1 }}</td>
                    <td>{{ trustScorePolicy.serviceTitle }}</td>
                    <td>{{ trustScorePolicy.trustScoreFactorTitle }}</td>
                    <td>{{ trustScorePolicy.scorePercent }} %</td>
                    <td>
                        <div style="width: 100%;">
                            <div class="float-left">
                                <i class="fa fa-pencil" style="font-size: 24px; color: green;" aria-hidden="true"></i>
                            </div>

                            <div class="float-right">
                                <i class="fa fa-times" 
                                 @click="deleteTrustScorePolicy(trustScorePolicy.id)"
                                 style="font-size: 24px; color: red;" aria-hidden="true"></i>
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Add New Trust Score Policy Modal -->
        <div class="modal fade" id="addNewTrustScorePolicyModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Modal title</h4>
            </div>
            <div class="modal-body">
                <table>
                    <tr>
                        <td>Service</td>
                        <td>
                            <select v-model="serviceId" class="form-control">
                                <option v-for="service in services" v-bind:value="service.id" 
                                :key="service.id">{{ service.serviceTitle }} - {{ service.servicePort }}</option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td>Trust Score Factor</td>
                        <td>
                            <select v-model="scoreFactorId" class="form-control">
                                <option v-for="trustScoreFactor in trustScoreFactors" 
                                v-bind:value="trustScoreFactor.scoreFactorId"
                                :key="trustScoreFactor.scoreFactorId">{{ trustScoreFactor.trustScoreFactorTitle }}</option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td>Policy Score Percent</td>
                        <td><input type="number" v-model="scorePercent" class="form-control"></td>
                    </tr>

                    <tr>
                        <td></td>
                        <td>
                            <button @click="addNewTrustScorePolicy()" class="btn btn-primary form-control">Add New Trust Score Policy</button>
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
    name: 'TrustScorePolicyComponent',

    data() {
      
        return {
        
            serviceId: '',

            scoreFactorId: '',

            scorePercent: '',

            trustScorePolicies: [],

            trustScoreFactors: [],

            services: [],

        }

    },

    methods: {

        async deleteTrustScorePolicy(trustScorePolicyId) {

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
                    
                    axios.post(this.$store.state.baseApi + "/api/v1/user/delete/trust/policy", {

                        'trustScorePolicyId': trustScorePolicyId

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
                                    'Trust Score Policy was Deleted Successfully',
                                    'success'
                                    ).then(function () {
                                        window.location.reload()
                                    })
                            }else {
                                Swal.fire(
                                    'Error!',
                                    'Failed to Delete Trust Score Policy. Please Try Again Later',
                                    'error'
                                    )
                            }
                        })
            

                }
                })

        },

        async pullTrustScoreFactors() {

            axios.get(this.$store.state.baseApi + "/api/v1/user/get/all/trust/score/factor/weights", 
                            { 
                            headers : {
                                'Content-Type': 'application/json',
                                userId: this.$store.getters.getAuthId,
                                authToken: this.$store.getters.getAuthToken
                            }

                        })
                        
                        .then( (response) => {

                            const resData = response.data

                            this.trustScoreFactors = resData.data

                            this.scoreFactorId = this.trustScoreFactors['0']['scoreFactorId'];
                        })

        },

        async pullServices() {

            axios.get(this.$store.state.baseApi + "/api/v1/user/services/get/all", 
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

                            this.serviceId = this.services['0']['id'];
                        })
        },

        async addNewTrustScorePolicy() {

            if (this.scorePercent != "") {

                 axios.post(this.$store.state.baseApi + "/api/v1/user/post/trust/score/policy", {

                            'serviceId': this.serviceId,
                            'scoreFactorId': this.scoreFactorId,
                            'scorePercent': this.scorePercent

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
                                    'New Trust Score Policy Was Added Successfully',
                                    'success'
                                    ).then(function () {
                                        window.location.reload();
                                    })

                            }else {
                                Swal.fire(
                                    'Error!',
                                    'Failed to Add Trust Score Policy. Please Try Again Later',
                                    'error'
                                    )
                            }
                        })

            }else {
                Swal.fire(
                        'Error!',
                        'Please Enter Policy Score Percent',
                        'error'
                        )
            }

        },

        async pullTrustScorePolicies() {

            axios.get(this.$store.state.baseApi + "/api/v1/user/trust/policies/get/all", 
                    { 
                    headers : {
                        'Content-Type': 'application/json',
                        userId: this.$store.getters.getAuthId,
                        authToken: this.$store.getters.getAuthToken
                    }

                })
                
                .then( (response) => {

                    const resData = response.data

                    this.trustScorePolicies = resData.data
                })
                
        }

    },

    created() {
        //Pull Trust Score Policies
        this.pullTrustScorePolicies()

        //Pull Services
        this.pullServices()

        //Pull Factors
        this.pullTrustScoreFactors()
    }
}

</script>

<style scoped>

</style>