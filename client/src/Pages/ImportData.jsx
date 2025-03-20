import React, { useState } from 'react';
import { FileInput, Label, Progress, Button, Alert } from "flowbite-react";

export default function ImportData() {
  const [file, setFile] = useState(null);
  const [uploading, setUploading] = useState(false);
  const [progress, setProgress] = useState(0);
  const [alert, setAlert] = useState({ show: false, type: '', message: '' });

  const handleFileChange = (e) => {
    if (e.target.files[0]) {
      setFile(e.target.files[0]);
      setAlert({ show: false });
    }
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    
    if (!file) {
      setAlert({ show: true, type: 'failure', message: 'Veuillez sélectionner un fichier.' });
      return;
    }

    if (file.type !== 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' && 
        file.type !== 'application/vnd.ms-excel') {
      setAlert({ show: true, type: 'failure', message: 'Veuillez sélectionner un fichier Excel (.xlsx ou .xls).' });
      return;
    }

    setUploading(true);
    setProgress(0);
    setAlert({ show: false });
    
    const formData = new FormData();
    formData.append('file', file);

    try {
      const interval = setInterval(() => {
        setProgress(prev => {
          if (prev >= 90) {
            clearInterval(interval);
            return 90;
          }
          return prev + 10;
        });
      }, 300);

      const response = await fetch('http://127.0.0.1:8000/api/importData', {
        method: 'POST',
        body: formData,
      });
      
      clearInterval(interval);
      setProgress(100);
      
      const data = await response.json();
      
      if (response.ok) {
        setAlert({ show: true, type: 'success', message: data.message || 'Données importées avec succès!' });
      } else {
        setAlert({ show: true, type: 'failure', message: data.error || "Une erreur est survenue lors de l'importation." });
        
        
      }
    } catch (error) {
      setAlert({ 
        show: true, 
        type: 'failure', 
        message: "Erreur de connexion avec le serveur. Veuillez vérifier que le serveur est en cours d'exécution." 
      });
      console.error("Upload error:", error);
    } finally {
      setUploading(false);
    }
  };

  return (
    <div className="container my-5">
      <div className="flex justify-center">
        <div className="w-full max-w-3xl">
          <div className="bg-white shadow-md rounded-lg p-6">
            <div className="mb-4">
              <h4 className="text-xl font-medium">Import Excel Data</h4>
            </div>
            
            {alert.show && (
              <Alert color={alert.type} className="mb-4">
                {alert.message}
              </Alert>
            )}
                 
            <form onSubmit={handleSubmit}>
              <div className="mb-4">
                <div className="mb-2 block">
                  <Label htmlFor="file" value="Fichier Excel" />
                </div>
                <FileInput 
                  id="file" 
                  onChange={handleFileChange}
                  helperText="Sélectionnez un fichier Excel (.xlsx ou .xls) à importer"
                  accept=".xlsx,.xls"
                />
              </div>
              
              {uploading && (
                <div className="mb-4">
                  <Progress progress={progress} size="lg" labelProgress labelText textLabel="Importation en cours..."/>
                </div>
              )}
              
              <Button type="submit" disabled={uploading}>
                {uploading ? 'Importation...' : 'Importer'}
              </Button>
            </form>
          </div>
        </div>
      </div>
    </div>
  );
}