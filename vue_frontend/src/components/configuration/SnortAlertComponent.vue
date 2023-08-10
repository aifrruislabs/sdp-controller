<template>
    <div class="row">
        
        <div class="col-md-12">

            <div class="container-fluid">

                <h4 class="left">Gateway Snort Alerts</h4>
                <hr/>
                
                <table class="table table-bordered">
                    <tr>
                        <td>ID</td>
                        <td>Source IP</td>
                        <td>Destination IP</td>
                        <td>Alert Code</td>
                        <td>Alert Title</td>
                        <td>Alert Classification</td>
                        <td>Alert Priority</td>
                        <td>Incident Response</td>
                        <td>Created</td>
                        <td>View More</td>
                    </tr>

                    <tr v-for="(snort_alert, id) in snort_alerts" :key=snort_alert.id>
                        <td>{{ id += 1 }}</td>
                        <td>{{ snort_alert.srcIP }}</td>
                        <td>{{ snort_alert.dstIp }}</td>
                        <td>{{ snort_alert.snortAlertCode }}</td>
                        <td>{{ snort_alert.snortAlertTitle }}</td>
                        <td>{{ snort_alert.snortAlertClassification }}</td>
                        <td>{{ snort_alert.snortAlertPriority }}</td>
                        <td>{{ snort_alert.incidentResponse }}</td>
                        <td>{{ snort_alert.createdAt }}</td>
                        <td>
                            <button class="btn btn-primary form-control">View More</button>
                        </td>
                    </tr>
                </table>

            </div>

        </div>

    </div>
</template>

<script>

    import axios from 'axios';

    export default {
    name: 'SnortAlertComponent',

    data() {
      
        return {
            
            'snort_alerts' : []

        }

    },

    methods: {

        //Pull Gateway Snort Alerts
        pullGatewaySnortAlerts() {
            
            axios.get(this.$store.state.baseApi + 
            
            "/api/v1/snort/read/gateway/alerts?gatewayId=" + this.$route.query.gatewayId, 

                { 
                    headers : {
                        'Content-Type': 'application/json',
                        userId: this.$store.getters.getAuthId,
                        authToken: this.$store.getters.getAuthToken
                    }

                })
                
                .then( (response) => {

                    const resData = response.data

                    this.snort_alerts = resData.data

                });

        },
        
    },

    created() {
        //Pull Gateway Snort Alerts
        this.pullGatewaySnortAlerts();
    }
}


</script>

<style scoped>

</style>