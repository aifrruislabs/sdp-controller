<template>
    <div>
        
        <div class="container-fluid">

            <div class="col-md-12">

                <div class="row">
                    <div class="col-md-10">
                        <h3>Users Manager</h3>
                    </div>

                    <div class="col-md-2">
                        <div class="float-right">
                            <i class="fa fa-plus" aria-hidden="true" style="margin-top: 30px; font-size: 20px;"></i>
                        </div>
                        
                    </div>
                </div>
                
                <hr/>

                <table class="table table-bordered">
                    <tr>
                        <td>ID</td>
                        <td>First Name</td>
                        <td>Last Name</td>
                        <td>Email</td>
                        <td>Actions</td>
                    </tr>
                </table>

            </div>

        </div>

    </div>
</template>

<script>

    export default {
    name: 'ClientsManagerComponent',

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