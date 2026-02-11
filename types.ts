
export enum Category {
  EPL = 'EPL',
  UCL = 'UCL',
  TRANSFERS = 'Transfers',
  CHELSEA = 'Chelsea',
  ARSENAL = 'Arsenal',
  LIVERPOOL = 'Liverpool',
  MANCITY = 'Man City',
  FIXTURES = 'Fixtures',
  TABLE = 'Table',
  SCORERS = 'Top Scorers'
}

export interface SEOMetadata {
  metaTitle: string;
  metaDescription: string;
  keywords: string;
}

export interface Post {
  id: string;
  title: string;
  excerpt: string;
  content: string;
  category: Category;
  author: string;
  date: string;
  image: string;
  isTopStory?: boolean;
  seo?: SEOMetadata;
}

export interface Match {
  id: string;
  homeTeam: string;
  homeLogo?: string;
  awayTeam: string;
  awayLogo?: string;
  homeScore?: number;
  awayScore?: number;
  time: string;
  status: 'SCHEDULED' | 'LIVE' | 'IN_PLAY' | 'PAUSED' | 'FINISHED' | 'POSTPONED' | 'CANCELLED';
  league: string;
}

export interface TableEntry {
  rank: number;
  team: string;
  teamLogo?: string;
  played: number;
  points: number;
  won: number;
  draw: number;
  lost: number;
  goalsFor: number;
  goalsAgainst: number;
  goalDifference: number;
}

export interface Scorer {
  rank: number;
  name: string;
  team: string;
  teamLogo?: string;
  goals: number;
  playedGames: number;
}

export interface SiteSettings {
  name: string;
  tagline: string;
  logo: string;
  adminEmail: string;
  smtpSender: string;
  smtpConfig?: {
    host: string;
    port: string;
    user: string;
    pass: string;
  };
  whatsappNumber: string;
  socials: {
    facebook: string;
    twitter: string;
    instagram: string;
    youtube: string;
  };
}

export type CommentStatus = 'pending' | 'approved' | 'rejected' | 'spam';

export interface Comment {
  id: string;
  postId: string;
  postTitle?: string;
  author: string;
  text: string;
  date: string;
  status: CommentStatus;
}

export interface Subscriber {
  email: string;
  dateJoined: string;
}
