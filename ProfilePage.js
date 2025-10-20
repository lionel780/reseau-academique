import React, { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import authService from './frontend/src/services/auth.service';
import { toast } from 'react-toastify';
import Header from './frontend/src/components/Header';
import Footer from './frontend/src/components/Footer';

const ProfilePage = () => {
    const [user, setUser] = useState(null);
    const [loading, setLoading] = useState(true);
    const [formData, setFormData] = useState({
        nom: '',
        prenom: '',
        email: '',
        currentPassword: '',
        newPassword: '',
        confirmPassword: ''
    });
    const [isEditing, setIsEditing] = useState(false);
    const navigate = useNavigate();
    
    useEffect(() => {
        const fetchUserProfile = async () => {
            try {
                setLoading(true);
                const currentUser = authService.getCurrentUser();
                
                if (!currentUser) {
                    navigate('/login');
                    return;
                }
                
                // Récupérer les informations détaillées de l'utilisateur
                const userInfo = currentUser.user || currentUser;
                setUser(userInfo);
                
                // Initialiser le formulaire avec les données de l'utilisateur
                setFormData({
                    nom: userInfo.nom || '',
                    prenom: userInfo.prenom || '',
                    email: userInfo.email || '',
                    currentPassword: '',
                    newPassword: '',
                    confirmPassword: ''
                });
                
                setLoading(false);
            } catch (error) {
                console.error('Erreur lors du chargement du profil:', error);
                toast.error('Impossible de charger votre profil');
                setLoading(false);
            }
        };
        
        fetchUserProfile();
    }, [navigate]);
    
    const handleChange = (e) => {
        const { name, value } = e.target;
        setFormData(prev => ({
            ...prev,
            [name]: value
        }));
    };
    
    const handleSubmit = async (e) => {
        e.preventDefault();
        
        // Vérifier si les mots de passe correspondent
        if (formData.newPassword && formData.newPassword !== formData.confirmPassword) {
            toast.error('Les mots de passe ne correspondent pas');
            return;
        }
        
        try {
            // Simuler la mise à jour du profil
            toast.success('Profil mis à jour avec succès');
            setIsEditing(false);
        } catch (error) {
            console.error('Erreur lors de la mise à jour du profil:', error);
            toast.error('Impossible de mettre à jour votre profil');
        }
    };
    
    if (loading) {
        return (
            <div className="flex flex-col min-h-screen">
                <Navbar />
                <div className="flex-grow container mx-auto px-4 py-8 flex justify-center items-center">
                    <div className="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500"></div>
                </div>
                <Footer />
            </div>
        );
    }
    
    return (
        <div className="flex flex-col min-h-screen">
            <Header />
            <div className="flex-grow container mx-auto px-4 py-8">
                <h1 className="text-2xl font-bold mb-6">Profil Utilisateur</h1>
                
                <div className="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div className="p-6">
                        <div className="flex items-center mb-6">
                            <div className="w-20 h-20 rounded-full bg-blue-500 flex items-center justify-center text-white text-2xl font-bold mr-4">
                                {user.prenom?.charAt(0)}{user.nom?.charAt(0)}
                            </div>
                            <div>
                                <h2 className="text-xl font-semibold">{user.prenom} {user.nom}</h2>
                                <p className="text-gray-600">{user.email}</p>
                                <p className="text-gray-600 capitalize">{user.role}</p>
                            </div>
                            <button 
                                className="ml-auto bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600"
                                onClick={() => setIsEditing(!isEditing)}
                            >
                                {isEditing ? 'Annuler' : 'Modifier'}
                            </button>
                        </div>
                        
                        {isEditing ? (
                            <form onSubmit={handleSubmit} className="space-y-4">
                                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 mb-1">
                                            Prénom
                                        </label>
                                        <input
                                            type="text"
                                            name="prenom"
                                            value={formData.prenom}
                                            onChange={handleChange}
                                            className="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        />
                                    </div>
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 mb-1">
                                            Nom
                                        </label>
                                        <input
                                            type="text"
                                            name="nom"
                                            value={formData.nom}
                                            onChange={handleChange}
                                            className="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        />
                                    </div>
                                </div>
                                
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-1">
                                        Email
                                    </label>
                                    <input
                                        type="email"
                                        name="email"
                                        value={formData.email}
                                        onChange={handleChange}
                                        className="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    />
                                </div>
                                
                                <div className="border-t pt-4 mt-4">
                                    <h3 className="text-lg font-medium mb-2">Changer le mot de passe</h3>
                                    
                                    <div className="space-y-4">
                                        <div>
                                            <label className="block text-sm font-medium text-gray-700 mb-1">
                                                Mot de passe actuel
                                            </label>
                                            <input
                                                type="password"
                                                name="currentPassword"
                                                value={formData.currentPassword}
                                                onChange={handleChange}
                                                className="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                            />
                                        </div>
                                        
                                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label className="block text-sm font-medium text-gray-700 mb-1">
                                                    Nouveau mot de passe
                                                </label>
                                                <input
                                                    type="password"
                                                    name="newPassword"
                                                    value={formData.newPassword}
                                                    onChange={handleChange}
                                                    className="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                />
                                            </div>
                                            <div>
                                                <label className="block text-sm font-medium text-gray-700 mb-1">
                                                    Confirmer le mot de passe
                                                </label>
                                                <input
                                                    type="password"
                                                    name="confirmPassword"
                                                    value={formData.confirmPassword}
                                                    onChange={handleChange}
                                                    className="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div className="flex justify-end">
                                    <button
                                        type="submit"
                                        className="bg-blue-500 text-white px-6 py-2 rounded-md hover:bg-blue-600 focus:outline-none"
                                    >
                                        Enregistrer
                                    </button>
                                </div>
                            </form>
                        ) : (
                            <div className="space-y-4">
                                <div className="border-t pt-4">
                                    <h3 className="text-lg font-medium mb-2">Informations personnelles</h3>
                                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <p className="text-sm text-gray-500">Prénom</p>
                                            <p className="font-medium">{user.prenom}</p>
                                        </div>
                                        <div>
                                            <p className="text-sm text-gray-500">Nom</p>
                                            <p className="font-medium">{user.nom}</p>
                                        </div>
                                        <div>
                                            <p className="text-sm text-gray-500">Email</p>
                                            <p className="font-medium">{user.email}</p>
                                        </div>
                                        <div>
                                            <p className="text-sm text-gray-500">Rôle</p>
                                            <p className="font-medium capitalize">{user.role}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        )}
                    </div>
                </div>
            </div>
            <Footer />
        </div>
    );
};

export default ProfilePage;
