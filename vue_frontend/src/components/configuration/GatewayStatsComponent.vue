<template>
    <div>

        <div class="container-fluid">

            <br/>
            <h4>Gateway Statistics</h4>
            <hr/>

            <div class="col-md-4 col-xs-4 well-bordered min-400-height center">
                <strong style="font-size: 20px;">Network Statistics Outside SDP</strong>
                <hr/>
                
                <VueSvgGauge
                    :start-angle="-110"
                    :end-angle="110"
                    :value=outsideSDPUsage
                    :separator-step="1"
                    :min="0"
                    :max="30"
                    :gauge-color="[{ offset: 0, color: 'green'}, { offset: 100, color: 'red'}]"
                    :scale-interval="1"
                    />

                <h2 class="center">{{ outsideSDPUsage }} MB/s</h2>
            </div>

            <div class="col-md-4 col-xs-4 well-bordered min-400-height center">
                <strong style="font-size: 20px;">Network Statistics Inside SDP</strong>
                <hr/>

                <VueSvgGauge
                    :start-angle="-110"
                    :end-angle="110"
                    :value=insideSDPUsage
                    :separator-step="1"
                    :min="0"
                    :max="30"
                    :gauge-color="[{ offset: 0, color: 'green'}, { offset: 100, color: 'red'}]"
                    :scale-interval="2"
                    />

                <h2 class="center">{{ insideSDPUsage }} MB/s</h2>
            </div>

            <div class="col-md-4 col-xs-4 well-bordered min-400-height center">
                <strong style="font-size: 20px;">Gateway CPU Statistics</strong>
                <hr/>

                <VueSvgGauge
                    :start-angle="-110"
                    :end-angle="110"
                    :value=cpuUsageGraph
                    :separator-step="1"
                    :min="0"
                    :max="50"
                    :gauge-color="[{ offset: 0, color: 'purple'}, { offset: 100, color: 'red'}]"
                    :scale-interval="2"
                    />

                <h2 class="center">{{ cpuUsage }} %</h2>
            </div>


        </div>
        
    </div>
</template>

<script>

import axios from 'axios'
import { VueSvgGauge } from 'vue-svg-gauge'

export default {
    name: 'GatewayStatsComponent',

    components: {
        VueSvgGauge,
    },

    data() {
      
        return {
            
            cpuUsage: 0,

            cpuUsageGraph: 0,

            insideSDPUsage: 0,

            outsideSDPUsage: 0,

        }

    },

    methods: {

        pullGatewayStats() {
            
            axios.get(this.$store.state.baseApi + 
            
            "/api/v1/get/gateway/network/traffic?gatewayId=" + this.$route.query.gatewayId, 

                { 
                    headers : {
                        'Content-Type': 'application/json',
                        userId: this.$store.getters.getAuthId,
                        authToken: this.$store.getters.getAuthToken
                    }

                })
                
                .then( (response) => {

                    const resData = response.data

                    const data = resData.data

                    this.insideSDPUsage = data['txCount']
                    this.outsideSDPUsage = data['rxCount']
                    this.cpuUsage = data['cpuPercent']

                    this.cpuUsageGraph = parseInt(this.cpuUsage) / 2

                });

        },

        pullGatewayRecs() {
            setInterval(function() {
                //Pull Gateway Stats
                this.pullGatewayStats()

            }, 2000)
        }

    }, 
    
    mounted() {
        
        setInterval(() => {
                this.pullGatewayStats()
        }, 2000) 

    }
}

</script>

<style scoped>

</style>