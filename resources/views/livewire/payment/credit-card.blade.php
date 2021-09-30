<div class="max-w-7xl mx-auto py-15 px-4" x-data="creditCard()" x-init="PagSeguroDirectPayment.setSessionId('{{$sessionId}}')">

    @include('includes.message')

    <div class="flex flex-wrap -mx-3 mb-6">

        <h2 class="w-full px-3 mb-6 border-b-2 border-cool-gray-800 pb-4">
           Realizar Pagamento Assinatura
        </h2>
    </div>

    <form action="" name="creditCard" class="w-full max-w-7xl mx-auto">

        <div class="flex flex-wrap -mx-3 mb-6">

            <p class="w-full px-3 mb-6">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">Número Cartão</label>
                <input @keyup="getBrand" type="text" name="card_number" class="block appearance-none w-full bg-gray-200 border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
            </p>

            <p class="w-full px-3 mb-6">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb2">Nome Cartão</label>
                <input type="text" name="card_name" class="block appearance-none w-full bg-gray-200 border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
            </p>

        </div>

        <div class="flex -mx-3">

            <p class="w-full px-3 mb-6">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb2">Mês Vencimento</label>
                <input type="text" name="card_month" class="block appearance-none w-full bg-gray-200 border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
            </p>

            <p class="w-full px-3 mb-6">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb2">Ano Vencimento</label>
                <input type="text" name="card_year" class="block appearance-none w-full bg-gray-200 border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
            </p>

        </div>

        <div class="flex flex-wrap -mx-3 mb-6">

            <p class="w-full px-3 mb-6">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb2">Código de Segurança</label>
                <input type="text" name="card_cvv" class="block appearance-none w-full bg-gray-200 border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
            </p>

            <p class="w-full py-4 px-3 mb-6">
                <button @click.prevent="cardToken" type="submit" class="flex-shrink-0 bg-teal-500 hover:bg-teal-700 border-teal-500 hover:border-teal-700 text-sm border-4 text-white py-1 px-2 rounded">Realizar Assinatura</button>
            </p>

        </div>

    </form>
</div>

<script type="text/javascript" src="https://stc.sandbox.pagseguro.uol.com.br/pagseguro/api/v2/checkout/pagseguro.directpayment.js"></script>

<script>

/*****
    - Ter uma sessão vinda do pagseguro -ok
    - Vai precisar importar o javascript do pagseguro - ok
    - Recuperar a bandeira do Cartão - ok
    - Precisar recuperar o token do cartão
    - enviar o token e o sender hash para o nosso componente php
*/

    function creditCard() {
        return {

            brandName: '',

            getBrand(e) {

                let cardNumber = e.target.value;

                if(cardNumber.length == 6) {

                    PagSeguroDirectPayment.getBrand({
                        cardBin: cardNumber,
                        success: (response) => {
                            //bandeira encontrada
                            this.brandName = response.brand.name;
                        }
                    })
                }
            },
            cardToken(e) {

                let formEl = document.querySelector('form[name=creditCard]');
                let formData = new FormData(formEl);

                //console.log(formData.get('card_name'));

                PagSeguroDirectPayment.createCardToken({
                    cardNumber: formData.get('card_number'), // Número do cartão de crédito
                    brand: this.brandName, // Bandeira do cartão
                    cvv: formData.get('card_cvv'), // CVV do cartão
                    expirationMonth: formData.get('card_month'), // Mês da expiração do cartão
                    expirationYear: formData.get('card_year'), // Ano da expiração do cartão, é necessário os 4 dígitos.
                    success: function(response) {
                            // Retorna o cartão tokenizado.
                            let payload = {
                                'token': response.card.token,
                                'senderHash': PagSeguroDirectPayment.getSenderHash(),
                            };

                            console.log(payload);

                            Livewire.emit('paymentData', payload);
                    },
                    error: function(response) {
                                // Callback para chamadas que falharam.
                    },
                    complete: function(response) {
                            // Callback para todas chamadas.
                            //console.log(response.card.token)
                    }
                    });
            }
        }
    }

</script>
