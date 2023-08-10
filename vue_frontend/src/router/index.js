import Vue from 'vue'
import Router from 'vue-router'
import { store } from '../store/store'

Vue.use(Router)

//Import Components
import TrustScoreWeightComponent from '@/components/configuration/TrustScoreWeightComponent.vue'
import TrustScorePolicyComponent from '@/components/configuration/TrustScorePolicyComponent.vue'

import IndexComponent from '@/components/pages/IndexComponent.vue'
import LoginComponent from '@/components/auth/LoginComponent.vue'
import CreateNewAccountComponent from '@/components/auth/CreateNewAccountComponent.vue'

import AboutComponent from '@/components/pages/AboutComponent.vue'
import DashboardComponent from '@/components/pages/DashboardComponent.vue'
import DemoVideosComponent from '@/components/pages/DemoVideosComponent.vue'

import GatewayStatsComponent from '@/components/configuration/GatewayStatsComponent.vue'
import ManageClientGeoLocationComponent from '@/components/configuration/ManageClientGeoLocationComponent.vue'
import ManageClientMacAddressComponent from '@/components/configuration/ManageClientMacAddressComponent.vue'

import SDPClientsComponent from '@/components/configuration/SDPClientsComponent.vue'
import SDPGatewayComponent from '@/components/configuration/SDPGatewayComponent.vue'

import SDPServicesComponent from '@/components/admin/SDPServicesComponent.vue'
import UsersManagerComponent from '@/components/pages/UsersManagerComponent.vue'
import FaceRecognitionManageFacesComponent from '@/components/pages/FaceRecognitionManageFacesComponent.vue'

import TutorialsComponent from '@/components/pages/TutorialsComponent.vue'
import FeaturesComponent from '@/components/pages/FeaturesComponent.vue'
import PricingComponent from '@/components/pages/PricingComponent.vue'

import TrustScoresComponent from '@/components/pages/TrustScoresComponent.vue'

import SnortAlertComponent from '@/components/configuration/SnortAlertComponent.vue'
import IncidentResponsePoliciesComponent from '@/components/configuration/IncidentResponsePolicies.vue'

const authMiddleware = (to, from, next) => {

	console.log(store.state.user.authToken)

	if (store.state.user.authToken != undefined) {
		if ( store.state.user.authToken.length == 0 ) {
			return next('/auth/login')
		}else {
			return next()
		}
		
	}

    return next();
}

//Router
export default new Router({
	
	mode: 'hash',

	routes: [

		//Snort Alerts
		{ path: '/snort/gateway/alerts', component: SnortAlertComponent, name: 'snort-gateway-alerts' },

		//Incident Response Policies
		{ path: '/incident/response/policies', component: IncidentResponsePoliciesComponent, name: 'incident-response-policies'},

		//Pricing
		{ path: '/pricing', component: PricingComponent, name: 'pricing' },

		//Tutorials
		{ path: '/tutorials', component: TutorialsComponent, name: 'tutorials' },

		//Features
		{ path: '/features', component: FeaturesComponent, name: 'features' },

		//View Gateway Statistics
		{ path: '/gateway/view/statistics', component: GatewayStatsComponent, name: 'gateway-stats' },

		//Manage Client Geo Location
		{ path: '/manage/clients/geo/location', component: ManageClientGeoLocationComponent, name: 'manage-client-geo-location' },

		//Manage Clients Mac Address
		{ path: '/manage/clients/mac/address', component: ManageClientMacAddressComponent, name: 'manage-client-mac-addr'},

		//Trust Score Policies
		{ path: '/trust/score/policies', component: TrustScorePolicyComponent, name: 'trust-score-policies' },

		//Trust Score Weights
		{ path: '/trust/score/weights', component: TrustScoreWeightComponent, name: 'trust-score-weights' },

		//Clients
		{ path: '/sdp/clients', component: SDPClientsComponent, name: 'sdp-clients' },

		//Services
		{ path: '/admin/services', component: SDPServicesComponent, name: 'sdp-services' },

		//Trust Scores
		{ path: '/trust/scores', component: TrustScoresComponent, name: 'trust-scores' },

		//Face Recognition Manage Faces
		{ path: '/face/recognition/manage/faces', component: FaceRecognitionManageFacesComponent, name: 'face-recognition-manage-faces'},

		//Users Manager
		{ path: '/users/manager', component: UsersManagerComponent, name: 'users-manager' },

		//SDP Gateway
		{ path: '/sdp/gateway', component: SDPGatewayComponent, name: 'sdp-gateway' },

		//SDP Services
		{ path: '/sdp/services', component: SDPServicesComponent, name: 'sd-services' },

		//Dashboard Route
		{ path: '/dashboard', component: DashboardComponent, name: 'dashboard', beforeEnter: authMiddleware },

		//Demo Videos
		{ path: '/demo/videos', component: DemoVideosComponent, name: 'demo-videos' },

        //Index Route
		{ path: '/', component: IndexComponent, name: 'index'},

		//About Route
		{ path: '/about', component: AboutComponent, name: 'about' },

		//Login Route
		{ path: '/auth/login', component: LoginComponent, name: 'login' },

        //Create New Account Route
		{ path: '/auth/create/new/account', component: CreateNewAccountComponent, name: 'create-new-account' },

    ]

})
