<template>
    <div>

        <div class="col-md-4">

        </div>

        <div class="col-md-4 col-md-offset-4">

        <h3>Create New Account</h3>
        <hr/>

        <table>
            <tr>
                <td>First Name</td>
                <td><input type="text" v-model="firstName" class="form-control"></td>
            </tr>

            <tr>
                <td>Last Name</td>
                <td><input type="text" v-model="lastName" class="form-control"></td>
            </tr>

            <tr>
                <td>Email</td>
                <td><input type="email" v-model="email" class="form-control"></td>
            </tr>

            <tr>
                <td>Password</td>
                <td><input type="password" v-model="password" class="form-control"></td>
            </tr>

            <tr>
                <td>Verify Password</td>
                <td><input type="password" v-model="password_verification" class="form-control"></td>
            </tr>

            <tr>
                <td></td>
                <td><input type="submit" class="btn btn-primary form-control" 
                    value="Create New Account" @click="createNewAccount"></td>
            </tr>
        </table>

        </div>

    </div>
</template>

<script>

import Swal from 'sweetalert2'
import axios from 'axios'
import router from './../../router/index'

export default {
    name: 'AboutComponent',

    data() {

        return {
            firstName: '',
            lastName: '',
            email: '',
            password: '',
            password_verification: ''
        }

    },

    methods: {

        async createNewAccount() {

            if ((this.firstName != "") && (this.lastName != "") && (this.email != "") && (this.password != "") &&
                this.password_verification != "") {
                
                if (this.password == this.password_verification) {
                        
                    await axios.post(this.$store.state.baseApi + "/api/v1/auth/register", {
                        firstName: this.firstName,
                        lastName: this.lastName,
                        email: this.email,
                        password: this.password
                    })
                    
                    .then( (response) => {

                        const data = response.data

                        if (data['status'] == true) {

                            this.firstName = this.lastName = this.email = this.password = this.password_verification = ''

                            Swal.fire({
                                title: 'Success!',
                                text: 'Your Account Was Created Successfully. Please Login to the Next Page',
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then(function () {
                                //Redirecting to Auth Login
                                router.push('/auth/login')
                            })

                        }else {
                            //Toast Wrong Password
                            Swal.fire({
                                title: 'Error!',
                                text: 'Error Occured. Please Try Again Later',
                                icon: 'warning',
                                confirmButtonText: 'OK'
                            })
                        }

                    }).catch( (error) => {
                        console.log(error)

                    });

                }else {
                    Swal.fire({
                            title: 'Warning!',
                            text: 'Password Mismatch. Please Enter Correct Password Confirmation',
                            icon: 'warning',
                            confirmButtonText: 'OK'
                        })
                }

            }else {
                Swal.fire({
                    title: 'Error!',
                    text: 'Please Fill in All the Blanks',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                })
            }

        }

    }
}



</script>
