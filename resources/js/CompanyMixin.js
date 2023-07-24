export default {

    methods:{
        activateCompany(company){

            let question = `Tem a certeza que quer activar empresa ${company.name}?`

            this.$confirm.require({
                message: question,
                header: 'Confirmar',
                icon: 'pi pi-exclamation-triangle',
                accept: () => {
                    axios.post(route('companies.activateCompany', company.id))
                        .then((response) => {
                            if(response.data.company_activated === true){
                                this.$toast.add({severity:'success', detail: 'Empresa activada com sucesso!', life: 3000})
                                company.status = 'active'
                                this.loadMenuItems()
                            }else{
                                this.$toast.add({severity:'error', detail: 'Ocorreu um erro ao activar a empresa!', life: 3000})
                            }
                        }).catch(error => {
                            console.log(error)
                        })
                },
            })
        },

        disableCompany(company){

            let question = `Tem a certeza que quer desactivar a empresa ${company.name}?`

            this.$confirm.require({
                message: question,
                header: 'Confirmar',
                icon: 'pi pi-exclamation-triangle',
                accept: () => {
                    axios.post(route('companies.disableCompany', company.id))
                        .then((response) => {
                            if(response.data.company_disabled === true){
                                this.$toast.add({severity:'success', detail: 'Empresa desactivada com sucesso!', life: 3000})
                                company.status = 'inactive'
                                this.loadMenuItems()
                            }else{
                                this.$toast.add({severity:'error', detail: 'Ocorreu um erro ao desactivar a empresa!', life: 3000})
                            }
                        }).catch(error => {
                            console.log(error)
                        })
                },
            })

        },

        deleteCompany(company){

            let question = `Tem a certeza que quer apagar a empresa ${company.name}?`

            this.$confirm.require({
                message: question,
                header: 'Confirmar',
                icon: 'pi pi-exclamation-triangle',
                accept: () => {
                    axios.delete(route('companies.destroy',  company.id))
                        .then((response) => {
                            if(response.data.company_deleted === true){
                                this.$toast.add({severity:'success', detail: 'Empresa apagada com sucesso!', life: 3000})
                                setTimeout(() => this.loadList(), 1000)
                            }else{
                                this.$toast.add({severity:'error', detail: 'Ocorreu um erro ao apagar a empresa! \n' + response.data.error, life: 3000})
                            }
                        }).catch(error => {
                            console.log(error)
                        })
                },
            })

        },

        suspendCompany(company){
            let question = `Tem a certeza que quer suspender a empresa ${company.name}?`

            this.$confirm.require({
                message: question,
                header: 'Confirmar',
                icon: 'pi pi-exclamation-triangle',
                accept: () => {
                    axios.post(route('companies.suspendCompany', company.id))
                        .then((response) => {
                            if(response.data.company_suspended === true){
                                this.$toast.add({severity:'success', detail: 'Empresa suspensa com sucesso!', life: 3000})
                                company.status = 'suspended'
                                this.loadMenuItems()
                            }else{
                                this.$toast.add({severity:'error', detail: 'Ocorreu um erro ao suspender a empresa!', life: 3000})
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
            case 'active':
                icon = 'text-green-500 pi pi-check-circle'
                break
            case 'inactive':
                icon = 'p-error pi pi-times-circle'
                break
            case 'suspended':
                icon = 'text-orange-500 fa-solid fa-ban'
                break
            }
            return icon
        },

        convertToGroupCompany(company) {
            let question = `Tem a certeza que quer converter para empresa de grupo, a empresa ${company.name}?`

            this.$confirm.require({
                message: question,
                header: 'Confirmar',
                icon: 'pi pi-exclamation-triangle',
                accept: () => {
                    axios.post(route('backoffice.companies.convertToGroupCompany', company.id))
                        .then((response) => {
                            if(response.data.company_converted === true){
                                this.$toast.add({severity:'success', detail: 'Empresa convertida com sucesso!', life: 3000})
                                this.loadMenuItems()
                                company.is_group_company = true
                            }else{
                                this.$toast.add({severity:'error', detail: 'Ocorreu um erro ao converter a empresa!', life: 3000})
                            }
                        }).catch(error => {
                            this.$toast.add({severity:'error', detail: 'Não tem permissões para converter a empresa!', life: 3000})
                            console.log(error)
                        })
                },
            })
        },

    },
}
