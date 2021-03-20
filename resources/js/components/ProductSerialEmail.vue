<template>
    <div class="d-inline-block">
        <button
            type="button"
            class="btn btn-secondary"
            :id="'open' + modalId"
            data-toggle="modal"
            :data-target="'#' + modalId"
        >
            <i class="fa fa-mail-bulk"></i>
            {{ title }}
        </button>

        <div class="modal fade" :id="modalId" tabindex="-1" role="dialog" :aria-labelledby="modalId" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <label>{{ t('validation.attributes.email') }}</label>
                        <div class="form-group" v-for="(email, i) in form.emails">
                            <div class="input-group">
                                <input
                                    type="text"
                                    :name="'email' + i"
                                    :id="'email' + i"
                                    class="form-control"
                                    :class="{'is-invalid': errors.has('email'  + i, 'productSerialEmail')}"
                                    v-validate
                                    data-vv-rules="required|email"
                                    data-vv-scope="productSerialEmail"
                                    v-model="form.emails[i]"
                                >
                                <span class="input-group-btn" v-if="i > 0">
                                    <button type="button" class="btn btn-danger" @click="removeEmail(i)">
                                        X
                                    </button>
                                </span>

                                <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('email' + i, 'required', 'productSerialEmail')">
                                    <strong>{{ t('validation.required', {attribute: 'email'}) }}</strong>
                                </span>
                                <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('email' + i, 'email', 'productSerialEmail')">
                                    <strong>{{ t('validation.email', {attribute: 'email'}) }}</strong>
                                </span>
                            </div>
                        </div>
                        <div>
                            <button type="button" class="btn btn-success" @click="addEmail()">
                                <i class="fa fa-plus"></i>
                                {{ t('form.addEmail') }}
                            </button>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button v-if="!loading" type="button" class="btn btn-primary" @click="validateForm()">{{ t('form.send') }}</button>
                        <button v-if="!loading" type="button" :id="'close' + modalId" class="btn btn-secondary" data-dismiss="modal">{{ t('form.close') }}</button>

                        <i class="spinner-border spinner-border-sm" v-if="loading"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import ApiService from "../services/ApiService";

    export default {
        name: 'ProductSerialEmail',
        props: {
            title: {
                type: String,
                required: true
            },

            editData: {
                type: Object,
                required: true
            }
        },

        data() {
            return {
                modalId: 'modal' + Math.round(Math.random() * 1000000),
                loading: false,
                form: {
                    emails: [null]
                }
            }
        },

        methods: {
            addEmail() {
                this.form.emails.push(null);
            },

            removeEmail(index) {
                this.form.emails.splice(index, 1);
            },

            validateForm() {
                this.$validator.validateAll('productSerialEmail').then(res => res && this.sendForm())
            },

            sendForm() {
                this.loading = true;

                ApiService.post('/warehouse/product-serial/' + this.editData.uuid + '/email', this.form).then(res => {
                    if (res.data.success) {
                        location.reload();
                    }

                }).catch(err => {
                    this.loading = false;
                })
            },
        }
    }
</script>
