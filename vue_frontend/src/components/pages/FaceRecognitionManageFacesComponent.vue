<template>
    <div>
        
        <div class="row">
            <div class="col-md-8">
                <h4>Manage Client Faces</h4>
            </div>

            <div class="col-md-4" >
                <div class="right">
                    <i class="fa fa-plus" style="font-size: 20px;" 
                        aria-hidden="true" data-toggle="modal" data-target="#addNewClientFaceModal"></i>
                </div>
            </div>
        </div>
        <hr/>

        <table class="table table-bordered">
            <tr>
                <td>ID</td>
                <td>Client First Name</td>
                <td>Client Last Name</td>
                <td>Client ID</td>
                <td>Client Username</td>
                <td>Client Face</td>
                <td>Actions</td>
            </tr>

            <tr v-for="(face, id) in faces" :key="face.id">
                <td>{{ id + 1}}</td>
                <td>{{ face.clientFirstName }}</td>
                <td>{{ face.clientLastName }}</td>
                <td>{{ face.clientId }}</td>
                <td>{{ face.clientUsername }}</td>
                <td class="center">
                    <img v-bind:src="face.faceLocation" class="img img-rounded" style="width: 150px; height: 150px;"/>
                </td>
                
                <td>
                    <div style="width: 100%;">
                        <div class="float-left">
                            <i class="fa fa-pencil" 
                             style="font-size: 24px; color: green;" aria-hidden="true"></i>
                        </div>

                        <div class="float-right">
                            <i class="fa fa-times" 
                            @click="deleteFace(face.id)"
                            style="font-size: 24px; color: red;" aria-hidden="true"></i>
                        </div>
                    </div>
                </td>

            </tr>

        </table>

        <!-- Add New Client Face Modal Modal -->
        <div class="modal fade" id="addNewClientFaceModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Add New Client Face</h4>
            </div>
            <div class="modal-body">
                <table>
                    <tr>
                        <td>Client ID</td>
                        <td>
                            <select class="form-control" v-model="clientId">
                                <option v-for="client in clients" v-bind:value="client.id" :key="client.id">
                                    {{ client.clientId }} - {{ client.firstName }} {{ client.lastName }}
                                </option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td>Face Image</td>
                        <td>
                            <input type="file" @change="changeFileToUpload" ref="file" class="form-control">
                        </td>
                    </tr>

                    <tr>
                        <td></td>
                        <td>
                            <button class="btn btn-primary form-control" 
                            @click="uploadFaceFile()">Add New Client Face</button>
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
    name: 'FaceRecognitionManageFacesComponent',

    data() {
      
        return {
            
            clientId: "",

            images: null,

            clients: [],

            faces: []

        }

    },

    methods: {

        //Delete Face
        async deleteFace(faceId) {

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
                    
                    axios.post(this.$store.state.baseApi + "/api/v1/user/delete/face/rec/data", {

                        'faceId': faceId

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
                                'Face was Deleted Successfully',
                                'success'
                                ).then(function () {
                                    window.location.reload()
                                })
                        }else {
                            Swal.fire(
                                'Error!',
                                'Failed to Delete Face. Please Try Again',
                                'error'
                                )
                        }

                    })

                }
            })

        },

        //Upload Face file to Server
        async uploadFaceFile() {

            const formData = new FormData();
            
            formData.append('clientId', this.clientId);
            formData.append('faceFile', this.Images);
            
            axios.post(this.$store.state.baseApi + "/api/v1/user/post/face/recognition/face", 
            formData, { headers : {
                                'Content-Type': 'multipart/form-data',
                                userId: this.$store.getters.getAuthId,
                                authToken: this.$store.getters.getAuthToken
                            } 
            }).then((response) => {

                var jsonData = JSON.parse(JSON.stringify(response.data))

                if (jsonData['status'] == true) {
                    Swal.fire(
                        'Success!',
                        'Client Face was Added Successfuly',
                        'success'
                        ).then(function () {
                            window.location.reload()
                        })
                }else {
                    Swal.fire(
                        'Error!',
                        'Failed to Add Client Face. Please Try Again Later',
                        'error'
                        )
                }

            });
        
        },

        //On File Change 
        changeFileToUpload() {
            this.Images = this.$refs.file.files[0];
        },

        //Get List of Clients
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
                    this.clientId = this.clients['0']['id']

                })

        },

        async getFacesList() {
            
            //Get Face Recognitions Faces List
            await axios.get(this.$store.state.baseApi + "/api/v1/user/sdp/get/all/face/rec/list", { 
                    
                        headers : {
                            'Content-Type': 'application/json',
                            userId: this.$store.getters.getAuthId,
                            authToken: this.$store.getters.getAuthToken
                        }

                })
                
                .then( (response) => {

                    const resData = response.data

                    this.faces = resData.data

                })

        }

    },

    created() {
        //Get Faces List
        this.getFacesList()

        //Get Clients List
        this.getClientsList()
    }
}


</script>

<style scoped>

</style>