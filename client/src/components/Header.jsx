import React from "react";
import { Navbar } from "flowbite-react";
import { Outlet } from "react-router-dom";
import { useEffect, useState } from "react";

export default function Header() {
    const [excelData, setExcelData] = useState([]);
    useEffect(() => {
        const fetchData = async () => {
            try {
                const response = await fetch(
                    "http://127.0.0.1:8000/api/AvencementParGroup"
                );

                if (!response.ok) {
                    throw new Error(
                        "La réponse du réseau n'était pas correcte"
                    );
                }

                const data = await response.json();
                setExcelData(data);
            } catch (error) {
                console.error(
                    "Erreur lors de la récupération des données :",
                    error
                );
            }
        };

        fetchData();
    }, []);
    return (
        <>
            <Navbar fluid rounded>
                <Navbar.Brand>
                    <img
                        src="/logo.jpg"
                        className="mr-3 h-20"
                        alt="Ofppt Logo"
                    />
                </Navbar.Brand>

                <Navbar.Collapse>
                    <div className="container m-5 mb-2">
                        <h1 className="text-base font-bold text-blue-700">
                            OFPPT
                        </h1>
                        <h2 className="text-sm text-gray-700">DR - BMK</h2>
                        <h2 className="text-sm">CFP BM 2</h2>
                        <h2 className="text-sm text-gray-700">ISTA NTIC BM</h2>
                    </div>
                </Navbar.Collapse>
            </Navbar>
            <div style={{ textAlign: "center", fontWeight: "bolder" }}>
                {excelData.length > 0 && localStorage.getItem('login')
                    ? <p>Date: {new Date(excelData[0].date_maj).toLocaleDateString(
                          "fr-FR",
                          {
                              day: "2-digit",
                              month: "2-digit",
                              year: "numeric",
                          }
                      )}</p>
                    : ""}
            </div>

            <Outlet />
        </>
    );
}
