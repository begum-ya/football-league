
import axios, { AxiosResponse } from 'axios';
import {api} from 'config/api';

import{
    TeamStats,
    Prediction,
    Fixture,
    Team,
    ApiResponse
} from 'types'

axios.defaults.withCredentials = true; // Çerezleri göndermek için

export const getCsrfCookie = async ():Promise<void>=> {
    return axios.get(`${import.meta.env.VITE_API_URL}/sanctum/csrf-cookie`,{withCredentials : true

    });
};

export const getTeams = async (): Promise<AxiosResponse<ApiResponse<Team[]>>> => {
    return api.get<ApiResponse<Team[]>>('/teams');
};
export const generateFixtures = async ():Promise<AxiosResponse<void>>=> {
    return api.post('/fixtures/generate');
};

export const getFixtures = async (): Promise<AxiosResponse<ApiResponse<Fixture[]>>> => {
    return api.get<ApiResponse<Fixture[]>>('/fixtures');
};

export const playWeek = async (week_id: number): Promise<AxiosResponse<void>> => {
    return api.post(`/simulation/play-week/${week_id}`,{ week_id });
};

export const playAllWeek = async (): Promise<AxiosResponse<void>> => {
    return api.post(`/simulation/play-all-weeks`);
};

export const getStandings = async (): Promise<AxiosResponse<ApiResponse<TeamStats[]>>> => {
    return api.get<ApiResponse<TeamStats[]>>('/standings');
};

export const getCurrentWeek = async (): Promise<AxiosResponse<{ success: boolean; current_week: number; week_title: string }>> => {
    return api.get<{ success: boolean; current_week: number; week_title: string }>('/fixtures/current-week');
};

export const getPredictions = async (): Promise<AxiosResponse<ApiResponse<Prediction[]>>> => {
    return api.get<ApiResponse<Prediction[]>>('/standings/predictions');
};

export const resetData = async ():Promise<void>=> {
    return api.post('/simulation/reset');
};