<template>
  <div class="container">
    <!--==================== SHOW INVOICE ====================-->
    <div class="invoices">
        
        <div class="card__header">
            <div>
                <h2 class="invoice__title">Invoice</h2>
            </div>
            <div>
                
            </div>
        </div>
        <div>
            <div class="card__header--title ">
                <h1 class="mr-2">#1043</h1>
                <p>{{ form.created_at }}</p>
            </div>
    
            <div>
                <ul  class="card__header-list">
                    <li>
                        <!-- Select Btn Option -->
                        <button class="selectBtnFlat" @click="print">
                            <i class="fas fa-print"></i>
                            Print
                        </button>
                        <!-- End Select Btn Option -->
                    </li>
                    <li>
                        <!-- Select Btn Option -->
                        <button class="selectBtnFlat" @click="onEdit(form.id)">
                            <i class=" fas fa-reply"></i>
                            Edit
                        </button>
                        <!-- End Select Btn Option -->
                    </li>
                    <li>
                        <!-- Select Btn Option -->
                        <button class="selectBtnFlat " @click="deleteInvoice(this.form.id)">
                            <i class=" fas fa-pencil-alt"></i>
                            Delete
                        </button>
                        <!-- End Select Btn Option -->
                    </li>
                    
                </ul>
            </div>
        </div>

        <div class="table invoice">
            <div class="logo">
                <img src="assets/img/logo.png" alt="" style="width: 200px;">
            </div>
            <div class="invoice__header--title">
                <p></p>
                <p class="invoice__header--title-1">Invoice</p>
                <p></p>
            </div>

            
            <div class="invoice__header--item">
                <div>
                    <h2>Invoice To:</h2>
                    <p v-if="form.customer">
                        {{ form.customer.firstname }}
                    </p>
                </div>
                <div>
                        <div class="invoice__header--item1">
                            <p>Invoice#</p>
                            <span>#{{ form.number }}</span>
                        </div>
                        <div class="invoice__header--item2">
                            <p>Date</p>
                            <span>{{ form.date }}</span>
                        </div>
                        <div class="invoice__header--item2">
                            <p>Due Date</p>
                            <span>{{ form.due_date }}</span>
                        </div>
                        <div class="invoice__header--item2">
                            <p>Reference</p>
                            <span>{{form.reference}}</span>
                        </div>
                    
                </div>
            </div>

            <div class="table py1">

                <div class="table--heading3">
                    <p>#</p>
                    <p>Item Description</p>
                    <p>Unit Price</p>
                    <p>Qty</p>
                    <p>Total</p>
                </div>
    
                <!-- item 1 -->
                <div class="table--items3" v-for="(data, i) in form.invoice_items" :key="data.id">
                    <p>{{i+1}}</p>
                    <p>{{ data.product.description }}</p>
                    <p>$ {{data.unit_price}}</p>
                    <p>{{ data.quantity }}</p>
                    <p>$ {{ data.unit_price * data.quantity}}</p>
                </div>
            </div>

            <div  class="invoice__subtotal">
                <div>
                    <h2>Thank you for your business</h2>   
                </div>
                <div>
                    <div class="invoice__subtotal--item1">
                        <p>Sub Total</p>
                        <span> $ {{form.sub_total}}</span>
                    </div>
                    <div class="invoice__subtotal--item2">
                        <p>Discount</p>
                        <span>$ {{form.discount}}</span>
                    </div>
                    
                </div>
            </div>

            <div class="invoice__total">
                <div>
                    <h2>Terms and Conditions</h2>
                    <p>{{ form.terms_and_condition }}</p>
                </div>
                <div>
                    <div class="grand__total" >
                        <div class="grand__total--items">
                            <p>Grand Total</p>
                            <span>$ {{form.total}}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
    <br>
  </div>
</template>

<script>
export default {
    data() {
        return{
            form: {id: ''}
        }
    },
    props: {
        id: {
        type: String,
        default: ''
        }
    },
    mounted() {
        axios
        .get(`/api/show_invoice/${this.id}`)
        .then((response) => {
          const form = response.data.invoice;
          this.form = form;
        })
        .catch((error) => {
          console.error("Terjadi kesalahan saat mengambil data invoice:", error);
        });
    },
    methods: {
        print() {
            window.print()
            this.$router.push('/').catch(() => {});
        },
        onEdit(id) {
            this.$router.push('/invoice/edit/' + id);
        },
        deleteInvoice(id) {
            const confirmDelete = confirm("Are you sure you want to delete this invoice?");
            if (confirmDelete) {
                axios
                    .delete(`/api/delete_invoice/` + id)
                    .then(() => {
                        // Jika berhasil dihapus, arahkan ke halaman lain atau lakukan operasi lainnya
                        this.$router.push("/");
                    })
                    .catch((error) => {
                        console.error("Failed to delete invoice:", error);
                    });
            }
        }
    }
}
</script>

<style>

</style>