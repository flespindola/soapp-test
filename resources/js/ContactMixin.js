import { Inertia } from '@inertiajs/inertia'
export default {

    methods:{
        disassociateContact(contact, company_id, company_name){

            let question = `Tem a certeza que quer remover a associação do contacto ${contact.fullname} à empresa ${company_name}?`

            this.$confirm.require({
                message: question,
                header: 'Confirmar',
                icon: 'pi pi-exclamation-triangle',
                accept: () => {
                    axios.post(route('contacts.disassociateContact', {
                        'contact': contact.id,
                        'company': company_id,
                    }))
                        .then((response) => {
                            if(response.data.association_removed === true){
                                this.$toast.add({
                                    severity:'success',
                                    detail: 'Associação removida com sucesso!',
                                    life: 3000
                                })
                                contact = null
                                Inertia.reload()
                            }else{

                                let error_msg = 'Ocorreu um erro ao remover a associação! ' + response.data.error_msg

                                this.$toast.add({
                                    severity:'error',
                                    detail: error_msg,
                                    life: 3000
                                })
                            }
                        }).catch(error => {
                            console.log(error)
                        })
                },
            })
        },
        activateContact(contact){

            let question = `Tem a certeza que quer activar o contacto ${contact.fullname}?`

            this.$confirm.require({
                message: question,
                header: 'Confirmar',
                icon: 'pi pi-exclamation-triangle',
                accept: () => {
                    axios.post(route('contacts.activateContact', contact.id))
                        .then((response) => {
                            if(response.data.contact_activated === true){
                                this.$toast.add({severity:'success', detail: 'Contacto activado com sucesso!', life: 3000})
                                contact.active = 1
                                this.loadMenuItems()
                            }else{
                                this.$toast.add({severity:'error', detail: 'Ocorreu um erro ao activar o contacto!', life: 3000})
                            }
                        }).catch(error => {
                            console.log(error)
                        })
                },
            })
        },

        disableContact(contact){

            let question = `Tem a certeza que quer desactivar o contacto ${contact.fullname}?`

            this.$confirm.require({
                message: question,
                header: 'Confirmar',
                icon: 'pi pi-exclamation-triangle',
                accept: () => {
                    axios.post(route('contacts.disableContact', contact.id))
                        .then((response) => {
                            if(response.data.contact_disabled === true){
                                this.$toast.add({severity:'success', detail: 'Contacto desactivado com sucesso!', life: 3000})
                                contact.active = 0
                                this.loadMenuItems()
                            }else{
                                this.$toast.add({severity:'error', detail: 'Ocorreu um erro ao desactivar o contacto!', life: 3000})
                            }
                        }).catch(error => {
                            console.log(error)
                        })
                },
            })
        },

        deleteContact(contact){

            let question = `Tem a certeza que quer apagar o contacto ${contact.fullname}?`

            this.$confirm.require({
                message: question,
                header: 'Confirmar',
                icon: 'pi pi-exclamation-triangle',
                accept: () => {
                    axios.delete(route('contacts.destroy',  contact.id))
                        .then((response) => {
                            if(response.data.contact_deleted === true){
                                this.$toast.add({severity:'success', detail: 'Contacto apagado com sucesso!', life: 3000})
                                Inertia.reload()
                            }else{
                                this.$toast.add({severity:'error', detail: 'Ocorreu um erro ao apagar o contacto! \n' + response.data.error, life: 3000})
                            }
                        }).catch(error => {
                            console.log(error)
                        })
                },
            })

        },

        getStatusIconClass(status){
            let icon = ''
            switch (status){
            case 1:
                icon = 'text-green-500 pi pi-check-circle'
                break
            case 0:
                icon = 'p-error pi pi-times-circle'
                break
            }
            return icon
        },
    },
}
