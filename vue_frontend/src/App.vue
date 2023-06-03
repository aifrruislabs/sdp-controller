<template>

	<div id="app">
		<HeaderComponent/>

		<router-view></router-view>

		<FooterComponent/>
	</div>

</template>

<script>

import HeaderComponent from '@/components/incs/HeaderComponent.vue'
import FooterComponent from '@/components/incs/FooterComponent.vue'

	export default {
		name: 'App',
		components: {
			'HeaderComponent': HeaderComponent,
			'FooterComponent': FooterComponent
		}
	}
</script>