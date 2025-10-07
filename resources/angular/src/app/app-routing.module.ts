import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import {AppComponent} from "./app.component";
import {NewPageComponent} from "./pages/new-page/new-page.component";

const routes: Routes = [
  {
    path: '',
    component: AppComponent,
    children: [
      { path: 'new-page', component: NewPageComponent }, // Новый маршрут
      { path: '**', redirectTo: '' } // Fallback
    ]
  }
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule { }
