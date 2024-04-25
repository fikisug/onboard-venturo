import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { PerfectScrollbarModule } from 'ngx-perfect-scrollbar';
import { NgbDropdownModule, NgbModule } from '@ng-bootstrap/ng-bootstrap';
import { ClickOutsideModule } from 'ng-click-outside';

import { LayoutComponent } from './layout.component';
import { FooterComponent } from './footer/footer.component';
import { HorizontalComponent } from './horizontal/horizontal.component';
import { HorizontaltopbarComponent } from './horizontaltopbar/horizontaltopbar.component';
import { NgProgressModule } from 'ngx-progressbar';
import { FormProfileComponent } from '../feature/user/components/form-profile/form-profile.component';
import { SharedModule } from "../shared/shared.module";
import { FormsModule } from '@angular/forms';

@NgModule({
    declarations: [LayoutComponent, FooterComponent, HorizontalComponent, HorizontaltopbarComponent, FormProfileComponent],
    exports: [],
    imports: [
        CommonModule,
        RouterModule,
        NgbDropdownModule,
        ClickOutsideModule,
        PerfectScrollbarModule,
        NgProgressModule,
        SharedModule,
        FormsModule,
        NgbModule
    ]
})
export class LayoutsModule { }
