<template>
    <div>
        <div class="row">
            <div class="col-md-8">
                <h4>Trust Score Weights</h4>
            </div>

            <div class="col-md-4" >
                <div class="right">
                    <i class="fa fa-plus" style="font-size: 20px;" 
                        aria-hidden="true" data-toggle="modal" data-target="#addNewTrustScoreWeightModal"></i>
                </div>
            </div>
        </div>
        <hr/>

        <div>

            <table class="table">
                <tr>
                    <td>ID</td>
                    <td>Score Factor</td>
                    <td>Score Weight</td>
                    <td>Actions</td>
                </tr>

                <tr v-for="(trustScoreWeight, id) in trustScoreWeightsList" :key="trustScoreWeight.id">
                    <td>{{ id + 1}}</td>
                    <td>{{ trustScoreWeight.trustScoreFactorTitle }}</td>
                    <td>{{ trustScoreWeight.scoreFactorPercent }} %</td>
                    <td>
                        <div style="width: 100%;">
                            <div class="float-left">
                                <i class="fa fa-pencil" style="font-size: 24px; color: green;" aria-hidden="true"></i>
                            </div>

                            <div class="float-right">
                                <i class="fa fa-times" 
                                @click="deleteTrustScoreWeight(trustScoreWeight.id)"
                                style="font-size: 24px; color: red;" aria-hidden="true"></i>
                            </div>
                        </div>

                    </td>
                </tr>

            </table>

        </div>


        <!-- Modal -->
        <div class="modal fade" id="addNewTrustScoreWeightModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">New Trust Score Weight</h4>
            </div>
            <div class="modal-body">
                <table>
                    <tr>
                        <td>Trust Score Factor</td>
                        <td>
                            <select class="form-control" v-model="trustScoreFactorId">
                                <option v-for="trustScoreFactor in trustScoreFactors" 
                                v-bind:value="trustScoreFactor.id" :key="trustScoreFactor.id">{{ trustScoreFactor.title }}</option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td>Trust Score Weight</td>
                        <td><input type="number" v-model="trustScoreWeight" class="form-control" placeholder="Trust Score Weight"></td>
                    </tr>

                    <tr>
                        <td></td>
                        <td>
                            <button @click="addNewTrustScoreWeight()" 
                            class="btn btn-primary form-control">Add New Trust Score Weight</button>
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
    name: 'TrustScoreWeightComponent',

    data() {
      
        return {
            
            trustScoreFactorId: 1,

            trustScoreWeight: '',

            trustScoreFactors: [],

            trustScoreWeightsList: [],

        }

    },

    methods: {

        async deleteTrustScoreWeight(trustScoreWeightId) {
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
                            
                    axios.post(this.$store.state.baseApi + "/api/v1/user/delete/trust/score/weight", {

                        'trustScoreWeightId': trustScoreWeightId

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
                                    'Trust Score Weight was Deleted Successfully',
                                    'success'
                                    ).then(function () {
                                        window.location.reload()
                                    })
                            }else {
                                Swal.fire(
                                    'Error!',
                                    'Failed to Delete Trust Score Weight. Please Try Again Later',
                                    'error'
                                    )
                            }

                        })
                        

                }
            })
        },

        async pullTrustScoreFactorsWeights() {

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

                    this.trustScoreWeightsList = resData.data

                })

        },

        async addNewTrustScoreWeight() {

            if (this.trustScoreWeight != "") {

                    axios.post(this.$store.state.baseApi + "/api/v1/user/post/trust/score/factor/weight", {

                        'trustScoreFactorId': this.trustScoreFactorId,

                        'trustScoreWeight': this.trustScoreWeight

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
                            'New Weight for the Factor was Added Successfully',
                            'success'
                            ).then(function () {
                                window.location.reload()
                            })                
                    }else if (resData['status'] == false && resData['error_code'] == 'DATA_EXISTS') {
                        Swal.fire(
                            'Warning!',
                            'This Factor Already Have a Weight. Please Update Factor Weight',
                            'warning'
                            ).then(function () {
                                window.location.reload()
                            })                
                    }else {
                        Swal.fire(
                            'Error!',
                            'Failed to Add New Weight for this Factor',
                            'error'
                            )                
                    }

                })                

            }else {
                Swal.fire(
                        'Error!',
                        'Please Enter Trust Score Factor and Its Weight',
                        'error'
                        )                
            }


        },

        async pullTrustScoreFactors() {

            axios.get(this.$store.state.baseApi + "/api/v1/user/get/trust/score/factors", 

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

            })
        }

    },

    created() {
        //Pull Trust Score Factors
        this.pullTrustScoreFactors()

        //Pull Trust Score Weights for the Factors
        this.pullTrustScoreFactorsWeights()
    }
}

</script>

<style scoped>

</style>