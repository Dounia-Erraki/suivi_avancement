import React from "react";
import { Navbar } from "flowbite-react";
import { Outlet } from "react-router-dom";

export default function Header() {
  return (
    <>
      <Navbar fluid rounded>
        <Navbar.Brand  >
          <img src="/logo.jpg" className="mr-3 h-20" alt="Ofppt Logo" />
        </Navbar.Brand>

        <Navbar.Collapse>
          <div className="container m-5 mb-2">
            <h1 className="text-base font-bold text-blue-700">OFPPT</h1>
            <h2 className="text-sm text-gray-700">DR - BMK</h2>
            <h2 className="text-sm">CFP BM 2</h2>
            <h2 className="text-sm text-gray-700">ISTA NTIC BM</h2>
          </div>
        </Navbar.Collapse>
      </Navbar>

      <Outlet />
    </>
  );
}
