<template>
  <div class="container">
    <div class="invoices">
        
        <div class="card__header">
            <div>
                <h2 class="invoice__title">Edit Invoice</h2>
            </div>
            <div>
                
            </div>
        </div>

        <div class="card__content">
            <div class="card__content--header">
                <div>
                    <p class="my-1">Customer</p>
                    <select v-model="form.customer_id" name="" id="" class="input">
                        <option disabled>Select Customer</option>
                        <option :value="data.id" v-for="data in customers" :key="data.id">
                        {{ data.firstname }}
                        </option>
                    </select>
                </div>
                <div>
                    <p class="my-1">Date</p> 
                    <input id="date" placeholder="dd-mm-yyyy" type="date" class="input" v-model="form.date"> <!---->
                    <p class="my-1">Due Date</p> 
                    <input id="due_date" type="date" class="input" v-model="form.due_date">
                </div>
                <div>
                    <p class="my-1">Numero</p> 
                    <input type="text" class="input" v-model="form.number"> 
                    <p class="my-1">Reference(Optional)</p> 
                    <input type="text" class="input" v-model="form.reference">
                </div>
            </div>
            <br><br>
            <div class="table">

                <div class="table--heading2">
                    <p>Item Description</p>
                    <p>Unit Price</p>
                    <p>Qty</p>
                    <p>Total</p>
                    <p></p>
                </div>
    
                <!-- item 1 -->
                <div class="table--items2" v-for="(item, i) in form.invoice_items" :key="item.id">
                    <p v-if="item.product">
                        # {{ item.product.item_code }} {{ item.product.description }}
                    </p>
                    <p v-else>
                        # {{ item.item_code }} {{ item.description }}
                    </p>
                    <p>
                        <input type="text" class="input"  v-model="item.unit_price">
                    </p>
                    <p>
                        <input type="text" class="input"  v-model="item.quantity">
                    </p>
                    <p>
                        $ {{ item.unit_price * item.quantity }}
                    </p>
                    <p style="color: red; font-size: 24px;cursor: pointer;" @click="deleteInvoiceItem(item.id, i)">
                        &times;
                    </p>
                </div>
                <div style="padding: 10px 30px !important;">
                    <button class="btn btn-sm btn__open--modal" @click="openModal">Add New Line</button>
                </div>
            </div>

            <div class="table__footer">
                <div class="document-footer" >
                    <p>Terms and Conditions</p>
                    <textarea cols="50" rows="7" class="textarea" v-model="form.terms_and_condition"></textarea>
                </div>
                <div>
                    <div class="table__footer--subtotal">
                        <p>Sub Total</p>
                        <span>$ {{subTotal}}</span>
                    </div>
                    <div class="table__footer--discount">
                        <p>Discount</p>
                        <input type="text" class="input" v-model="form.discount">
                    </div>
                    <div class="table__footer--total">
                        <p>Grand Total</p>
                        <span>$ {{ total }}</span>
                    </div>
                </div>
            </div>

           
        </div>
        <div class="card__header" style="margin-top: 40px;">
            <div>
                
            </div>
            <div>
                <a class="btn btn-secondary" @click="onEdit">
                    Save
                </a>
            </div>
        </div>
        
    </div>
    <!--==================== add modal items ====================-->
    <div class="modal main__modal " :class="{ show: showModal }">
        <div class="modal__content">
            <span class="modal__close btn__close--modal" @click="closeModal">×</span>
            <h3 class="modal__title">Add Item</h3>
            <hr><br>
            <div class="modal__items">
                <ul style="list-style: none;">
                    <li
                        v-for="(mama, i) in listProduct"
                        :key="mama.id"
                        style="display: grid; grid-template-columns: 30px 350px 15px; align-items: center; padding-bottom: 5px;"
                    >
                        <p>{{ i + 1 }}</p>
                        <a href="#">{{ mama.item_code }} {{ mama.description }}</a>
                        <button
                        @click="addCart(mama)"
                        style="border: 1px solid #e0e0e0; width: 35px; height: 35px; cursor: pointer;"
                        >
                        +
                        </button>
                    </li>
                </ul>
            </div>
            <br><hr>
            <div class="model__footer">
                <button class="btn btn-light mr-2 btn__close--modal" @click="closeModal">
                    Cancel
                </button>
                <button class="btn btn-light btn__close--modal " @click="onSave">Save</button>
            </div>
        </div>
    </div>
  </div>
</template>

<script>

export default {
    data() {
        return {
            customers: [],
            form: {},
            listProduct: [],
            showModal: false,
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
          console.log(this.form);
        })
        .catch((error) => {
          console.error("Terjadi kesalahan saat mengambil data invoice:", error);
        });

        axios
        .get("/api/customers")
        .then((response) => {
          const customers = response.data.customers;
          console.log(customers)
          this.customers = customers;
        })
        .catch((error) => {
          console.error("Terjadi kesalahan saat mengambil data invoice:", error);
        });

        axios
        .get("/api/products")
        .then((response) => {
          const products = response.data.products;
          this.listProduct = products;
        })
        .catch((error) => {
          console.error("Terjadi kesalahan saat mengambil data invoice:", error);
        });
    },
    methods: {
        deleteInvoiceItem(id,i) {
            this.form.invoice_items.splice(i,1)
            if(id != undefined){
                axios
                .get("/api/delete_invoice_items/" + id)
                .then((response) => {
                console.log(response)
                })

                .catch((error) => {
                console.error("Terjadi kesalahan saat mengambil data invoice:", error);
                });
            }
        },
        openModal() {
            this.showModal = true;
        },
        closeModal() {
            this.showModal = false;
        },
        addCart(item) {
            const itemCart = {
            item_code: item.item_code,
            description: item.description,
            product_id: item.id,
            unit_price: item.unit_price,
            quantity: item.quantity,
            };
            this.form.invoice_items.push(itemCart);
            this.closeModal();
        },
        onEdit() {
            if (this.form.invoice_items.length >= 1) {
                const invoiceData = {
                    invoice_item: JSON.stringify(this.form.invoice_items),
                    customer_id: this.form.customer_id,
                    date: this.form.date,
                    due_date: this.form.due_date,
                    number: this.form.number,
                    reference: this.form.reference,
                    discount: this.form.discount,
                    sub_total: this.subTotal,
                    total: this.total,
                    terms_and_condition: this.form.terms_and_condition,
                };

                axios
                .post(`/api/update_invoice/${this.form.id}`, invoiceData)
                .then(() => {
                    this.$router.push("/");
                })
                .catch((error) => {
                    console.error("Failed to save invoice:", error);
                    console.log(invoiceData)
                    console.log(error.response);  // Tambahkan ini
                }); 
            }
        }
    },
    computed: {
        subTotal() {
            let total = 0;
            if(this.form.invoice_items){
                this.form.invoice_items.forEach((data) => {
                total += data.quantity * data.unit_price;
                });
            }
            return total;
        },
        total() {
            return this.subTotal - this.form.discount;
        },
    }

}
</script>

<style>

</style>