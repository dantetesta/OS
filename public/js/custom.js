// Funções personalizadas para o sistema

// Confirmação de exclusão
function confirmDelete(event, message = 'Tem certeza que deseja excluir este item?') {
    if (!confirm(message)) {
        event.preventDefault();
        return false;
    }
    return true;
}

// Formatação de moeda
function formatCurrency(value) {
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    }).format(value);
}

// Formatação de data
function formatDate(dateString) {
    return new Date(dateString).toLocaleDateString('pt-BR');
}

// Máscara para telefone
function maskPhone(input) {
    let value = input.value.replace(/\D/g, '');
    value = value.replace(/^(\d{2})(\d)/g, '($1) $2');
    value = value.replace(/(\d)(\d{4})$/, '$1-$2');
    input.value = value;
}

// Alpine.js Data
document.addEventListener('alpine:init', () => {
    // Componente de filtro de tabela
    Alpine.data('tableFilter', () => ({
        search: '',
        items: [],
        filteredItems: [],
        
        init() {
            this.items = JSON.parse(this.$el.dataset.items || '[]');
            this.updateFilter();
        },
        
        updateFilter() {
            const searchTerm = this.search.toLowerCase();
            this.filteredItems = this.items.filter(item => 
                Object.values(item).some(value => 
                    String(value).toLowerCase().includes(searchTerm)
                )
            );
        }
    }));
    
    // Componente de formulário
    Alpine.data('form', () => ({
        loading: false,
        
        async submit() {
            this.loading = true;
            try {
                await this.$el.submit();
            } catch (error) {
                console.error('Erro ao enviar formulário:', error);
                alert('Ocorreu um erro ao processar sua solicitação.');
            } finally {
                this.loading = false;
            }
        }
    }));
});
