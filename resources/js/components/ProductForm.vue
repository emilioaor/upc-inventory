<template>
    <div class="container">
        <form class="techland-form" @submit.prevent="validateForm()">
            <div class="card">
                <div class="card-header">
                    <div v-if="editData">
                        <i class="fa fa-edit"></i> {{ t('form.edit') }} {{ t('menu.products') }}
                    </div>
                    <div v-else>
                        <i class="fa fa-plus"></i> {{ t('form.add') }} {{ t('menu.users') }}
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6 col-lg-4 form-group">
                            <label for="upc">{{ t('validation.attributes.upc') }}</label>
                            <input
                                type="text"
                                id="upc"
                                name="upc"
                                class="form-control"
                                :class="{'is-invalid': errors.has('upc')}"
                                v-model="form.upc"
                                v-validate
                                data-vv-rules="required"
                                :readonly="true"
                            >

                            <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('upc', 'required')">
                                <strong>{{ t('validation.required', {attribute: 'upc'}) }}</strong>
                            </span>
                        </div>

                        <div class="col-sm-6 col-lg-4 form-group">
                            <label for="sku">{{ t('validation.attributes.sku') }}</label>
                            <input
                                type="text"
                                id="sku"
                                name="sku"
                                class="form-control"
                                :class="{'is-invalid': errors.has('sku')}"
                                v-model="form.sku"
                                v-validate
                                :readonly="true"
                            >

                            <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('sku', 'required')">
                                <strong>{{ t('validation.required', {attribute: 'sku'}) }}</strong>
                            </span>
                        </div>
                        
                        <div class="col-sm-6 col-lg-4 form-group">
                            <label for="name">{{ t('validation.attributes.name') }}</label>
                            <input
                                type="text"
                                id="name"
                                name="name"
                                class="form-control"
                                :class="{'is-invalid': errors.has('name')}"
                                v-model="form.name"
                                v-validate
                                data-vv-rules="required"
                            >

                            <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('name', 'required')">
                                <strong>{{ t('validation.required', {attribute: 'name'}) }}</strong>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button v-if="!loading" class="btn btn-success">
                        <i class="fa fa-save"></i>
                        {{ t('form.save') }}
                    </button>

                    <i v-if="loading" class="spinner-border spinner-border-sm"></i>
                </div>
            </div>
        </form>
    </div>
</template>

<script>
    import ApiService from "../services/ApiService";

    export default {
        props: {
            editData: {
                type: Object,
                required: false
            }
        },

        mounted() {
            if (this.editData) {
                this.form = {...this.editData};
            }
        },

        data() {
            return {
                loading: false,
                form: {
                    upc: null,
                    sku: null,
                    name: null
                }
            }
        },

        methods: {
            validateForm() {
                this.$validator.validateAll().then(res => res && this.sendForm());
            },

            sendForm() {
                this.loading = true;

                const apiService = this.editData ?
                    ApiService.put('/admin/product/' + this.editData.uuid, this.form) :
                    ApiService.post('/admin/product', this.form)
                ;

                apiService.then(res => {
                    if (res.data.success) {
                        location.href = res.data.redirect;
                    }
                }).catch(err => {
                    this.loading = false;
                })
            }
        }
    }
</script>
