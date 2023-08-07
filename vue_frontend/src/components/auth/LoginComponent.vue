<template>
    
    <div>

        <div class="col-md-4">

        </div>

        <div class="col-md-4 col-md-offset-4">
            
            <h3>Account Login</h3>
            <hr/>

            <table>
                <tr>
                    <td>Email</td>
                    <td><input type="text" @keyup.enter="login" v-model="email" class="form-control"></td>
                </tr>

                <tr>
                    <td>Password</td>
                    <td><input type="password" @keyup.enter="login" v-model="password" class="form-control"></td>
                </tr>

                <tr>
                    <td></td>
                    <td><input type="submit" class="btn btn-primary form-control" value="Login" @click="login"></td>
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
            email: '',
            password: ''
        }

    },

    methods: {

        async login() {
        
            if (this.email != "" && this.password != "") {

                await axios.post(this.$store.state.baseApi + "/api/v1/auth/login", {
                    email: this.email,
                    password: this.password
                })
                
                .then( (response) => {

                    const resData = response.data

                    const data = resData.data

                    if (resData['status'] == true) {

                        this.email = this.password = ''

                        this.$store.state.user.firstName = data['firstName']
                        this.$store.state.user.lastName = data['lastName']

                        //Setting Token
                        this.$store.commit('successAuth', { 
                                                'token' : data['authToken'], 
                                                'userId' : data['id'],
                                                'userLevel' : data['level']
                                            } )

                        //Redirecting to Dashboard
                        router.push('/dashboard')

                    }else {
                        this.password = ''

                        //Toast Wrong Password
                        Swal.fire({
                            title: 'Error!',
                            text: 'Please Enter Correct Email and Password Combination',
                            icon: 'warning',
                        })
                    }

                }).catch( (error) => {
                    console.log(error)

                });

            }else {
                Swal.fire({
                    title: 'Error!',
                    text: 'Please Enter Email and Password',
                    icon: 'warning',
                })
            }

        }

    }
}


</script>