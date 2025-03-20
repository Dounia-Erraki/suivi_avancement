import { createBrowserRouter, createRoutesFromElements, Route, RouterProvider } from "react-router-dom";
import Layout from "./components/Layout";
import FormateursRendement from "./Pages/FormateursRendement";
import ImportData from "./Pages/ImportData";
import AffectationModule from "./Pages/AffectationModule";
import Header from "./components/Header";


function App() {
  const router = createBrowserRouter(
    createRoutesFromElements(
      <Route path="/"  element={<Layout/>}>
        <Route path="/"  element={<Header/>}>
          <Route path="FormateursRendementController" element={<FormateursRendement/>} />
          <Route path="AffectationController" element={<AffectationModule/>} />
          <Route path="importData" element={<ImportData/>} />
          <Route path="*" element={<ImportData/>}/> 
        </Route>
      </Route>
    )
  )
  return <RouterProvider router={router} />
}

export default App;
