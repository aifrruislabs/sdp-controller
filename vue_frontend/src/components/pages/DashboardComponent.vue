<template>
    <div>
        
        <div class="row">
            <div class="col-sm-6 col-md-3">
                <router-link to="/sdp/gateway">
                    <div class="thumbnail center">        
                        <img src="/pics/sdp_gateways.png" 
                        alt="" style="width: 150px; height: 150px;">
                        
                        <div class="caption">
                            <h4>SDP Gateways</h4>
                        </div>
                    </div>
                </router-link>
            </div>

            <div class="col-sm-6 col-md-3">
                <router-link to="/sdp/clients">
                    <div class="thumbnail center">
                        <img src="/pics/sdp_clients.png" 
                        alt="" style="width: 150px; height: 150px;">
                        <div class="caption">
                            <h4>SDP Clients</h4>
                        </div>
                    </div>
                </router-link>
            </div>

            <div class="col-sm-6 col-md-3">
                <router-link to="/trust/score/weights">
                    <div class="thumbnail center">
                        <img src="/pics/trust_score_weights.jpg" 
                        alt="" style="width: 150px; height: 150px;">
                        <div class="caption">
                            <h4>Trust Score Weights</h4>
                        </div>
                    </div>
                </router-link>
            </div>

            <div class="col-sm-6 col-md-3">
                <router-link to="/trust/score/policies">
                    <div class="thumbnail center">
                        <img src="/pics/trust_score_policies.png" 
                        alt="" style="width: 150px; height: 150px;">
                        <div class="caption">
                            <h4>Trust Score Policies</h4>
                        </div>
                    </div>
                </router-link>
            </div>
        
        </div>

        <div class="row container">
            
            <h3>Activities Logs</h3>
            <hr/>

            <table class="table table-bordered">
                <tr>
                    <td>ID</td>
                    <td>Activity Title</td>
                    <td>Activity Info</td>
                    <td>Time</td>
                </tr>
            </table>

        </div>

    </div>
</template>

<script>
export default {
    name: 'DashboardComponent',

    data() {

        return {
            
        }

    },

    methods: {

    }
}

</script>

<style scoped>

</style>
