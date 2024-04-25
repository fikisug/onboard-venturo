import { Component, EventEmitter, OnInit, Output, QueryList, SimpleChange, ViewChild, ViewChildren } from '@angular/core';
import { CustomerService } from "../../../customer/services/customer.service";
import { ProductService } from "../../../product/product/services/product.service";
import { NgbModal } from "@ng-bootstrap/ng-bootstrap";
import { OwlOptions } from "ngx-owl-carousel-o";
import { PromoService } from "../../../promo/services/promo.service";
import { CdkDragDrop, moveItemInArray } from '@angular/cdk/drag-drop';
import Swal from 'sweetalert2';
import { NgxSpinnerService } from 'ngx-spinner';
import { LandaService } from 'src/app/core/services/landa.service';
import { SaleService } from '../../services/sale.service';
import { NgSelectComponent } from '@ng-select/ng-select';
import { max } from 'rxjs';

@Component({
    selector: 'app-list-sale',
    templateUrl: './list-sale.component.html',
    styleUrls: ['./list-sale.component.scss']
})
export class ListSaleComponent implements OnInit {
    @ViewChild('customerDropDown') customerDropDown: NgSelectComponent;
    @ViewChildren('detailDropDown') detailDropDowns: QueryList<NgSelectComponent>;

    loadingCustomer: boolean;
    Disabled: boolean = true;
    loadingProgressProduct: number;
    showLoader: boolean = false;

    maxDiscountPromo = null;
    maxVoucherPromo = null;

    customers: Array<{
        id: string,
        name: string
    }> = [];

    products: Array<{
        m_product_id: string,
        name: string,
        price: string,
        photo_url: string,
        is_available: string,
        details: Array<{
            id: string,
            name: string,
            price: number,
            description: string,
            type: string,
        }>,
    }>

    selectedCustomer: any;
    selectedDetails: any;

    discountCustomer: Array<{
        id: string,
        name: string,
        nominal_percentage: number,
        term_conditions: string,
    }> = [];
    voucherCustomer: Array<{
        id: string,
        name: string,
        nominal_rupiah: number,
        term_conditions: string,
    }> = [];
    productOrders: Array<{
        m_product_id: string,
        name: string,
        price: number,
        photo_url: string,
        total_item: number,
        discount_nominal: number,
        detail_id: string,
        detail_type: string,
        detail_description: string,
        detail_price: number,
    }> = [];

    subtotal: number = 0;
    tax: number = 0;
    nominalDiscount: number = 0;
    totalBayar: number = 0;

    filter: {
        product_name: '',
    };

    productId: string;
    customerId: string;

    formModel: {
        id: string,
        no_struk: string,
        m_customer_id: string,
        m_voucher_id: string,
        voucher_nominal: number,
        total_voucher: number,
        m_discount_id: string,
        date: string,
        details: Array<{
            m_product_id: string,
            m_product_detail_id: string,
            name: string,
            price: number,
            total_item: number,
            discount_nominal: number,
        }>,
    };

    constructor(
        private customerService: CustomerService,
        private productService: ProductService,
        private saleService: SaleService,
        private promoService: PromoService,
        private modalService: NgbModal,
        private spinner: NgxSpinnerService,
        private landaService: LandaService,
    ) {
    }

    ngOnInit(): void {
        this.setDefaultFilter();
        this.getCustomers();
        this.getProducts('');
    }

    // ngOnChanges(changes: SimpleChange) {
    //     this.setDefaultFilter();
    //     this.getCustomers();
    //     this.getProducts('');
    // }

    drop(event: CdkDragDrop<string[]>) {
        moveItemInArray(this.productOrders, event.previousIndex, event.currentIndex);
    }

    setDefaultFilter() {
        this.filter = {
            product_name: '',
        }
    }

    getCustomers() {
        this.loadingCustomer = true;

        this.customerService.getUsers().subscribe({
            next: (res: any) => {
                this.customers = res.data.list;
                this.loadingCustomer = false;
            },
            error: (err: any) => {
                console.error(err);
            }
        });
    }

    getProducts(name: '') {
        this.loadingProgressProduct = 0;
        this.productService.getProducts({ name: name, is_available: '1' }).subscribe({
            next: (res: any) => {
                this.products = res.data.list.map((product: any) => {
                    return { ...product, m_product_id: product.id, details: product.details };
                });
                this.loadingProgressProduct = 100;
            },
            error: (err: any) => {
                console.error(err);
                this.loadingProgressProduct = 100;
            }
        });
    }

    getPromoByCustomer() {
        this.spinner.show();
        this.promoService.getPromo({}).subscribe({
            next: (res: any) => {
                const promos = res.data.list;

                this.discountCustomer = [];
                this.voucherCustomer = [];

                const currentDate = new Date().toLocaleDateString('en-CA');

                for (const promo of promos) {
                    for (const discount of this.selectedCustomer.discount) {
                        if (discount.promo_id === promo.id && promo.status == 'Diskon') {
                            this.discountCustomer.push(promo);
                            break;
                        }
                    }

                    for (const voucher of this.selectedCustomer.voucher) {

                        const startTime = new Date(voucher.start_time).toLocaleDateString('en-CA');
                        const endTime = new Date(voucher.end_time).toLocaleDateString('en-CA');

                        if (voucher.promo_id === promo.id && promo.status == 'Voucher' && currentDate >= startTime && currentDate <= endTime && voucher.total_voucher > 0) {
                            this.voucherCustomer.push(promo);
                            break;
                        }
                    }
                }

                let maxPercentage = 0;
                let maxRupiah = 0;

                for (const diskon of this.discountCustomer) {
                    if (diskon.nominal_percentage > maxPercentage) {
                        const matchedDiskon = this.selectedCustomer.discount.find(disc => disc.promo_id === diskon.id);
                        if (matchedDiskon) {
                            maxPercentage = diskon.nominal_percentage;
                            this.maxDiscountPromo = { ...diskon, m_discount_id: matchedDiskon.id };
                        }
                    }
                }

                for (const voucher of this.voucherCustomer) {
                    if (voucher.nominal_rupiah > maxRupiah) {
                        const matchedVoucher = this.selectedCustomer.voucher.find(voucherr => voucherr.promo_id === voucher.id && voucherr.total_voucher > 0);
                        if (matchedVoucher && matchedVoucher.total_voucher > 0) {
                            maxRupiah = voucher.nominal_rupiah;
                            this.maxVoucherPromo = { ...voucher, m_voucher_id: matchedVoucher.id };
                            this.formModel.total_voucher = matchedVoucher.total_voucher - 1;
                        }
                    }
                }

                if (this.maxDiscountPromo != null) {
                    this.formModel.m_discount_id = this.maxDiscountPromo.m_discount_id;
                }

                if (this.maxVoucherPromo != null) {
                    this.formModel.m_voucher_id = this.maxVoucherPromo.m_voucher_id;
                    this.formModel.voucher_nominal = this.maxVoucherPromo.nominal_rupiah;
                }

                this.showLoader = false;
                this.Disabled = false;
                this.spinner.hide();
            },
            error: (err: any) => {
                this.showLoader = false;
                this.Disabled = false;
                this.spinner.hide();
                console.error(err);
            }
        });
    }

    resetData() {
        this.subtotal = 0;
        this.tax = 0;
        this.nominalDiscount = 0;
        this.totalBayar = 0;
        this.productOrders = [];
        this.discountCustomer = [];
        this.voucherCustomer = [];
        this.maxDiscountPromo = null;
        this.maxVoucherPromo = null;

        this.formModel = {
            id: '',
            no_struk: '',
            m_customer_id: '',
            m_voucher_id: '',
            voucher_nominal: 0,
            total_voucher: 0,
            m_discount_id: '',
            date: '',
            details: [],
        };
        this.setNoStruk();
    }

    onCustomerChange(event) {
        this.showLoader = true;
        if (event !== undefined) {
            this.selectedCustomer = event;
            this.Disabled = true;

            this.resetData();
            this.formModel.m_customer_id = this.selectedCustomer.id;
            this.getPromoByCustomer();
        } else {
            this.showLoader = false;
            this.Disabled = true;
            this.selectedCustomer = null;
            this.resetData();
        }

        // if (this.selectedCustomer !== null && this.selectedCustomer.discount.length != 0) {

        // }
    }

    hitungTotal() {
        let nominalPromo = this.maxVoucherPromo ? this.maxVoucherPromo.nominal_rupiah : 0;
        this.totalBayar = this.subtotal + this.tax + this.nominalDiscount - nominalPromo;
        if (this.totalBayar < 0) {
            this.totalBayar = 0;
        }
    }

    addMenuOrder(product, idDetail, detailDropDown) {
        let productExists = false;
        if (idDetail == '') {
            this.selectedDetails = null;
        }
        // if (this.selectedCustomer !== null && this.selectedCustomer !== undefined) {
        for (const productOrder of this.productOrders) {
            if (productOrder.m_product_id === product.id && productOrder.detail_id === idDetail) {
                productOrder.total_item++;
                productExists = true;

                //update formModel details
                for (const products of this.formModel.details) {
                    if (products.m_product_id === productOrder.m_product_id && products.m_product_detail_id === productOrder.detail_id) {
                        products.total_item = productOrder.total_item;
                        products.discount_nominal += this.maxDiscountPromo ? (this.maxDiscountPromo.nominal_percentage / 100) * (product.price + productOrder.detail_price) : 0;
                    }
                }

                this.subtotal += product.price + productOrder.detail_price;
                this.tax = this.subtotal * 0.11;
                this.nominalDiscount = this.maxDiscountPromo ? -((this.maxDiscountPromo.nominal_percentage / 100) * this.subtotal) : 0;
                this.hitungTotal();
                break;
            }
        }

        if (!productExists) {
            const newProductOrder = {
                ...product, total_item: 1,
                detail_id: this.selectedDetails ? this.selectedDetails.id : '',
                detail_type: this.selectedDetails ? this.selectedDetails.type : '',
                detail_description: this.selectedDetails ? this.selectedDetails.description : '',
                detail_price: this.selectedDetails ? this.selectedDetails.price : 0
            };
            this.productOrders.push(newProductOrder);
            //push product details
            const newDetail = {
                m_product_id: newProductOrder.id,
                m_product_detail_id: newProductOrder.detail_id,
                name: newProductOrder.name,
                price: newProductOrder.price,
                total_item: newProductOrder.total_item,
                discount_nominal: this.maxDiscountPromo ? (this.maxDiscountPromo.nominal_percentage / 100) * (newProductOrder.price + newProductOrder.detail_price) : 0,
            };
            this.formModel.details.push(newDetail);

            this.subtotal += newProductOrder.price + newProductOrder.detail_price;
            this.tax = this.subtotal * 0.11;
            this.nominalDiscount = this.maxDiscountPromo ? -((this.maxDiscountPromo.nominal_percentage / 100) * this.subtotal) : 0;
            this.hitungTotal();
        }
        this.addMenu = false;
        if (detailDropDown) {
            detailDropDown.blur();
            detailDropDown.clearModel();
        }
    }

    addQty(product) {
        for (const productOrder of this.productOrders) {
            if (productOrder.m_product_id === product.id && productOrder.detail_id === product.detail_id) {
                productOrder.total_item++;

                //update formModel details
                for (const products of this.formModel.details) {
                    if (products.m_product_id === productOrder.m_product_id && products.m_product_detail_id === productOrder.detail_id) {
                        products.total_item = productOrder.total_item;
                        products.discount_nominal += this.maxDiscountPromo ? (this.maxDiscountPromo.nominal_percentage / 100) * (product.price + productOrder.detail_price) : 0;
                    }
                }

                this.subtotal += product.price + productOrder.detail_price;
                this.tax = this.subtotal * 0.11;
                this.nominalDiscount = this.maxDiscountPromo ? -((this.maxDiscountPromo.nominal_percentage / 100) * this.subtotal) : 0;
                this.hitungTotal();
                break;
            }
        }
    }

    reduceQty(product) {
        // console.log(this.productOrders)
        let i = 0;
        for (const products of this.productOrders) {
            if (products.m_product_id === product.id && products.detail_id === product.detail_id) {
                if (products.total_item > 1) {
                    products.total_item--;

                    //update total_item details
                    for (const productDetail of this.formModel.details) {
                        if (productDetail.m_product_id === products.m_product_id && productDetail.m_product_detail_id === products.detail_id) {
                            productDetail.total_item = products.total_item;
                            productDetail.discount_nominal -= this.maxDiscountPromo ? (this.maxDiscountPromo.nominal_percentage / 100) * (product.price + products.detail_price) : 0;
                        }
                    }

                    this.subtotal -= products.price + products.detail_price;
                    this.tax = this.subtotal * 0.11;
                    this.nominalDiscount = this.maxDiscountPromo ? -((this.maxDiscountPromo.nominal_percentage / 100) * this.subtotal) : 0;
                    this.hitungTotal();
                } else {
                    this.subtotal -= products.price + products.detail_price;
                    this.tax = this.subtotal * 0.11;
                    this.nominalDiscount = this.maxDiscountPromo ? -((this.maxDiscountPromo.nominal_percentage / 100) * this.subtotal) : 0;
                    this.hitungTotal();

                    //splice product details
                    const detailIndex = this.formModel.details.findIndex(detail => detail.m_product_id === products.m_product_id);
                    if (detailIndex !== -1) {
                        this.formModel.details.splice(detailIndex, 1);
                    }

                    this.productOrders.splice(i, 1); // Menghapus elemen di indeks i
                }
                break;
            }
            i++; // Tingkatkan indeks i untuk mencocokkan iterasi
        }
    }

    addMenu: boolean = false;
    onProductDetailPick(event, product, index) {
        const detailDropDown = this.detailDropDowns.toArray()[index];
        if (event != undefined) {
            this.selectedDetails = event;
            this.addMenu = true;
        }
        if (this.addMenu == true) {
            this.addMenuOrder(product, this.selectedDetails ? this.selectedDetails.id : '', detailDropDown);
        }
    }

    clearFilterProduct() {
        this.filter.product_name = '';
        this.getProducts('');
    }

    updateProduct(modalId, productId) {
        this.productId = productId;
        this.modalService.open(modalId, { size: 'xl', backdrop: 'static' });
    }

    updateCustomer(modalId, customer_id) {
        this.customerId = customer_id;
        this.modalService.open(modalId, { size: 'xl', backdrop: 'static' });
    }

    updateCustomerName(customer_id) {
        this.customerService.getUsers({ id: customer_id }).subscribe({
            next: (res: any) => {
                this.selectedCustomer.name = res.data.list.map(item => item.name);
            },
            error: (err: any) => {
                console.error(err);
            }
        });
    }

    setNoStruk() {
        const currentDate = new Date(); // Mendapatkan tanggal dan waktu saat ini
        const currentYear = currentDate.getUTCFullYear(); // Mendapatkan tahun UTC saat ini
        const currentMonthNumber = currentDate.getUTCMonth() + 1; // Mendapatkan bulan UTC saat ini (penambahan 1 karena bulan dimulai dari 0)
        const currentMonth = currentMonthNumber < 10 ? '0' + currentMonthNumber : currentMonthNumber.toString();
        const no = '001';

        this.saleService.getSale({ sort: '-created_at' }).subscribe(
            (res: any) => {
                if (res.data.list.length > 0) {
                    const lastTransactionStruk = res.data.list[0].no_struk;
                    const lastTransactionMonth = new Date(res.data.list[0].date).getMonth() + 1;
                    const lastTransactionYear = new Date(res.data.list[0].date).getFullYear();

                    if (lastTransactionStruk && currentMonthNumber == lastTransactionMonth && currentYear == lastTransactionYear) {
                        let nextStrukNumber = parseInt(lastTransactionStruk) + 1;
                        let nextStruk = String(nextStrukNumber).padStart(3, '0');
                        this.formModel.no_struk = `${nextStruk}/KWT/${currentMonth}/${currentYear}`;
                    } else {
                        this.formModel.no_struk = `${no}/KWT/${currentMonth}/${currentYear}`;
                    }
                } else {
                    this.formModel.no_struk = `${no}/KWT/${currentMonth}/${currentYear}`;
                }
            },
            (err) => {
                console.error(err);
            }
        );
    }

    save() {
        this.showLoader = true;
        this.Disabled = true;
        const currentDate = new Date().toISOString().slice(0, 19).replace('T', ' ');
        this.formModel.date = currentDate;

        this.saleService.createSale(this.formModel).subscribe((res: any) => {
            this.landaService.alertSuccess('Berhasil', res.message);
            this.showLoader = false;
            this.Disabled = false;
            this.resetData();
            this.selectedCustomer = null;
            this.customerDropDown.clearModel();
            this.getCustomers();
        }, err => {
            this.showLoader = false;
            this.Disabled = false;
            this.landaService.alertError('Mohon Maaf', err.error.errors);
        });
    }

    customOptions: OwlOptions = {
        margin: 10,
        dots: false,
        nav: false,
        autoWidth: true,
    };
}