import Vue from 'vue'
import Vuex from 'vuex'
import createPersistedState from 'vuex-persistedstate'

Vue.use(Vuex)

export const store = new Vuex.Store({

	plugins: [createPersistedState()],

	state: {
		
		baseApi: 'https://sdpapi.aifrruislabs.com',

		// baseApi: 'http://137.184.29.107:12800',

		// baseApi: 'http://172.17.0.2:8000',

		// baseApi: 'http://127.0.0.1:8000',

		user: {
			authId: '',

			authToken: '',

			level: '',

			firstName: '',

			lastName: '',

			email: '',
		}
		
	},

	mutations: {

		authLogout: state => {
			state.user.authId = ''
			state.user.authToken = ''
			state.user.firstName = ''
			state.user.lastName = ''
			state.user.email = ''
		},

		//Set Token and Other Info
		successAuth: (state, payload) => {

			state.user.authId = payload.userId
			state.user.level = payload.userLevel
			state.user.authToken = payload.token

		},

	},

	actions: {

	},

	getters: {

		//Get Auth Id
		getAuthId: state => {
			return state.user.authId
		},

		getUserLevel: state => {
			return state.user.level
		},

		//Get Auth Token
		getAuthToken: state => {
			return state.user.authToken
		},
		
		//Check if User is Authenticated
		isAuthenticated: state => {
			
			if (state.user.authToken.length != 0) {
				return true
			}else {
				return false
			}
			
		}

	}

})
