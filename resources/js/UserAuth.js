export default {

    killUserSessions(user, logged_user, $confirm, $toast, callback){

        //Verificar se estamos a matar as nossas proprias sessões
        let kill_own_sessions = (logged_user.id == user.id)

        let question = `Tem a certeza que quer limpar as sessões do utlizador ${user.name}?`
        if(kill_own_sessions){
            question = 'Tem a certeza que quer limpar as suas próprias sessões?'
        }

        $confirm.require({
            message: question,
            header: 'Confirmar',
            icon: 'pi pi-exclamation-triangle',
            accept: () => {
                axios.post(route('backoffice.users.killUserSessions'), {
                    user_id: user.id,
                }).then((response) => {
                    if(response.data.sessions_killed === true){
                        $toast.add({severity:'success', detail: 'Sessões limpas com sucesso!', life: 3000})
                        if(kill_own_sessions){
                            window.location.reload()
                        }else{
                            if(callback != undefined){
                                callback()
                            }
                        }
                    }else{
                        $toast.add({severity:'error', detail: 'Ocorreu um erro ao limpar as sessões!', life: 3000})
                    }
                }).catch(error => {
                    console.log(error)
                })
            },
            reject: () => {
                //callback to execute when user rejects the action
            }
        })
    },
}
