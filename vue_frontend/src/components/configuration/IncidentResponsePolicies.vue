<template>
    <div class="container-fluid">
        
        <div class="col-md-12">

            <div class="col-md-8">
                <h4>Incident Response Policies</h4>
            </div>

            <div class="col-md-4" >
                <div class="right">
                    <i class="fa fa-plus" style="font-size: 20px;" 
                        aria-hidden="true" data-toggle="modal" data-target="#addNewSnortClassTypeModal"></i>
                </div>
            </div>
           
        </div>
        <br/> <hr/>
       

        <table class="table table-bordered">
            <tr>
                <td>ID</td>
                <td>Incident Class Type</td>
                <td>Incident Response</td>
                <td>Action</td>
            </tr>

            <tr v-for="(incident_policy, id) in icd_policies_list" :key="incident_policy.id">
                <td>{{ id += 1 }}</td>
                <td>{{ incident_policy.incidentClassTypeDescription }}</td>
                <td>{{ incident_policy.incidentResponseDescription }}</td>
                <td>
                    <div style="width: 100%;">
                        <div class="float-left">
                            <i class="fa fa-pencil" style="font-size: 24px; color: green;" aria-hidden="true"></i>
                        </div>

                        <div class="float-right">
                            <i class="fa fa-times" @click="deleteICDPolicy(incident_policy.id)"
                                style="font-size: 24px; color: red;" aria-hidden="true"></i>
                        </div>
                    </div>
                </td>
            </tr>

        </table>


        <!-- Modal -->
        <div class="modal fade" id="addNewSnortClassTypeModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Add New Incident Response</h4>
                </div>
                <div class="modal-body">
                    <table>
                        <tr>
                            <td>Gateway</td>
                            <td>
                                <select class="form-control" v-model="form_gateway">
                                    <option value="0">All Gateway</option>
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <td>Incident Class Type</td>
                            <td>
                                <select class="form-control" v-model="form_snort_class">
                                    <option v-for="snort_class in snort_classes" :key="snort_class.id" :value="snort_class.id">
                                        {{ snort_class.description }}
                                    </option>
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <td>Incident Response</td>
                            <td>
                                <select class="form-control" v-model="form_icd_response">
                                    <option v-for="incident_response in gateway_incident_response" 
                                    :key="incident_response.id" :value="incident_response.id">{{ incident_response.description }}</option>
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <td></td>
                            <td>
                                <button @click="addNewIncidentResponsePolicy()" class="btn btn-primary form-control">Add New Incident Response</button>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
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
    name: 'IncidentResponsePolicies',

    data() {
      
        return {

            'form_gateway': 0,
            'form_snort_class': 1,
            'form_icd_response': 1,
            'snort_classes': [],
            'gateway_incident_response': [],
            'icd_policies_list': []
            
        }

    },

    methods: {

        //Delete Incident Policy 
        deleteICDPolicy(incident_response_id) {
            
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
                        axios.delete(this.$store.state.baseApi + "/api/v1/delete/icd/response/policy?icdPolicyId="+incident_response_id,
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
                                    'Incident Response Policy was Deleted Successfully',
                                    'success'
                                    ).then(function () {
                                        window.location.reload()
                                    });
                            }else {
                                Swal.fire(
                                    'Error!',
                                    'Failed to delete Incident Response Policy. Please Try Again Later',
                                    'error'
                                    )
                            }

                        })
                    }
                })
            
        },

        //Add New Incident Response Policy
        addNewIncidentResponsePolicy() {

            axios.post(this.$store.state.baseApi + 
            
            "/api/v1/new/icd/response/policy",  {

                'gateway': this.form_gateway,
                'snort_class': this.form_snort_class,
                'icd_response': this.form_icd_response
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
                            'New Incident Policy Was Added Successfully',
                            'success'
                            ).then(function () {
                                window.location.reload();
                            })

                    }else {
                        Swal.fire(
                            'Error!',
                            'Failed to Add New Incident Policy.',
                            'error'
                            )
                    }

                });
            
        },


        //Pull ICD Response Policy
        async pullICDResponcePolicies() {

            axios.get(this.$store.state.baseApi + 
            
            "/api/v1/get/icd/responses/policies", 

                { 
                    headers : {
                        'Content-Type': 'application/json',
                        userId: this.$store.getters.getAuthId,
                        authToken: this.$store.getters.getAuthToken
                    }

                })
                
                .then( (response) => {

                    const resData = response.data

                    this.icd_policies_list = resData.data

                });
            
        },

        //Pull Snort ICD Responses
        async pullSnortICDResponses() {

            axios.get(this.$store.state.baseApi + 
            
            "/api/v1/get/snort/icd/responses", 

                { 
                    headers : {
                        'Content-Type': 'application/json',
                        userId: this.$store.getters.getAuthId,
                        authToken: this.$store.getters.getAuthToken
                    }

                })
                
                .then( (response) => {

                    const resData = response.data

                    this.gateway_incident_response = resData.data

                });

        },

        //Pull Snort Class Types
        async pullSnortClassTypes() {
            
            axios.get(this.$store.state.baseApi + 
            
            "/api/v1/get/snort/classification/list", 

                { 
                    headers : {
                        'Content-Type': 'application/json',
                        userId: this.$store.getters.getAuthId,
                        authToken: this.$store.getters.getAuthToken
                    }

                })
                
                .then( (response) => {

                    const resData = response.data

                    this.snort_classes = resData.data

                });

         }

    }, 

    created() {
        //Pull Snort Class Types
        this.pullSnortClassTypes();

        //Pull Snort ICD Responses
        this.pullSnortICDResponses();

        //Pull ICD Policies
        this.pullICDResponcePolicies();
    }
}


</script>

<style scoped>

</style>