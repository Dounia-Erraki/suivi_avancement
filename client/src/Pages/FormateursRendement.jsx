import React, { useEffect, useState } from 'react';
import { useNavigate } from "react-router-dom";
import { Table, Card, Spinner, Alert, Button } from "flowbite-react";
import { HiInformationCircle } from "react-icons/hi";

export default function FormateursRendement() {
  const [excelData, setExcelData] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const navigate = useNavigate();
  
  useEffect(() => {
    const fetchData = async () => {
      try {
        setLoading(true);
        const response = await fetch("http://127.0.0.1:8000/api/FormateursRendementController");
        
        if (!response.ok) {
          throw new Error("La réponse du réseau n'était pas correcte");
        }
        
        const data = await response.json();
        setExcelData(data);
      } catch (error) {
        setError('Échec de la récupération des données', error);
      } finally {
        setLoading(false);
      }
    };
    
    fetchData();
  }, []);
  
  useEffect(() => {
    if (!loading && !excelData.length && !error) {
      navigate('/importData');
    }
  }, [excelData, loading, navigate, error]);

  if (loading) {
    return (
      <div className="flex flex-col items-center">
        <Spinner size="xl" />
        <p className="mt-4 text-gray-600">Chargement des données...</p>
      </div>
    );
  }

  if (error) {
    return (
      <div className="flex flex-col items-center gap-4">
        <Alert color="failure" icon={HiInformationCircle}>
          <span className="font-medium mb-4">{error}</span>
        </Alert>
        <Button color="blue" onClick={() => navigate('/importData')}>
          Importer des données
        </Button>
      </div>
    );
  }

  return (
    <div className="mx-auto p-4">
      <Card>
        <h2 className="text-2xl font-bold text-black mb-4">Rendement des Formateurs</h2>
        
        <div className="overflow-x-auto">
          <Table striped hoverable className="text-black text-center text-xs">
            <Table.Head className="bg-gray-100">
              <Table.HeadCell>Mle Formateur</Table.HeadCell>
              <Table.HeadCell>Nom & Prénom Formateur</Table.HeadCell>
              <Table.HeadCell>MHP Totale</Table.HeadCell>
              <Table.HeadCell>MHSYN Totale</Table.HeadCell>
              <Table.HeadCell>MH Totale</Table.HeadCell>
              <Table.HeadCell>MHP Réalisée</Table.HeadCell>
              <Table.HeadCell>MHSYN Réalisée</Table.HeadCell>
              <Table.HeadCell>MH Totale Réalisée</Table.HeadCell>
              <Table.HeadCell>Rendement en %</Table.HeadCell>
            </Table.Head>

            <Table.Body className="divide-y">
              {excelData.map((data, index) => {

                const totalHours = data.mhp_totale + data.mhsyn_totale;
                const totalRealizedHours = data.mhp_realisee + data.mhsyn_realisee;
                
                const rendement = totalRealizedHours > 0 
                  ? Math.round((totalRealizedHours / totalHours) * 100)
                  : 0;
                
                return (
                  <Table.Row key={index} className="bg-white">
                    <Table.Cell className="text-left">{data.mle_formateur}</Table.Cell>
                    <Table.Cell className="whitespace-nowrap text-left">{data.nom_formateur}</Table.Cell>
                    <Table.Cell>{data.mhp_totale}</Table.Cell>
                    <Table.Cell>{data.mhsyn_totale}</Table.Cell>
                    <Table.Cell>{totalHours}</Table.Cell>
                    <Table.Cell>{data.mhp_realisee}</Table.Cell>
                    <Table.Cell>{data.mhsyn_realisee}</Table.Cell>
                    <Table.Cell>{totalRealizedHours}</Table.Cell>
                    <Table.Cell>
                      <span className={`font-bold ${rendement >= 80 ? 'text-green-600' : rendement >= 60 ? 'text-yellow-600' : 'text-red-600'}`}>
                        {rendement}%
                      </span>
                    </Table.Cell>
                  </Table.Row>
                );
              })}
            </Table.Body>
          </Table>
        </div>
      </Card>
    </div>
  );
}